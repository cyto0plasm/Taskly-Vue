<template>
  <section id="See_How_It_Works" class="relative w-full h-screen bg-black overflow-hidden flex items-center justify-center">
    <video
      ref="videoRef"
      class="w-full h-full object-cover"
      :src="videoSrc"
      preload="auto"
      muted
      playsinline
      @timeupdate="onTimeUpdate"
      @play="updatePlayPauseIcon"
      @pause="updatePlayPauseIcon"
      @loadedmetadata="onVideoReady"
      @seeked="onSeeked"
      @waiting="onBuffering"
      @canplay="onCanPlay"
    ></video>

    <!-- Dark overlay -->
    <div class="absolute inset-0 bg-black/30 pointer-events-none"></div>

    <!-- Top bar: step label + toggle -->
    <div class="absolute top-8 left-1/2 -translate-x-1/2 flex items-center gap-3">
      <div class="bg-black/50 text-white text-sm font-semibold px-5 py-2 rounded-full backdrop-blur-sm tracking-wide">
        Step {{ currentStep + 1 }} of {{ steps.length }} • {{ steps[currentStep].title }}
      </div>
      <button
        @click="toggleMode"
        class="bg-black/50 hover:bg-black/70 text-white text-xs font-semibold px-4 py-2 rounded-full backdrop-blur-sm border border-white/20 transition-all duration-200"
        :title="fullMode ? 'Switch to chapter mode' : 'Switch to full video'"
      >
        {{ fullMode ? '⊞ Chapter Mode' : '▶ Full Video' }}
      </button>
    </div>

    <!-- Step progress bar -->
    <div class="absolute top-0 left-0 right-0 h-1 bg-white/10">
      <div
        class="h-full bg-[#7433ed] transition-none"
        :style="{ width: progressWidth }"
      ></div>
    </div>

    <!-- Fade overlay for transitions -->
    <div
      class="absolute inset-0 bg-black pointer-events-none transition-opacity duration-250 z-10"
      :style="{ opacity: fadeOverlayOpacity }"
    ></div>

    <!-- Progress dots -->
    <div class="absolute bottom-24 left-1/2 -translate-x-1/2 flex gap-2" :class="{ 'hidden': fullMode }">
      <button
        v-for="(step, index) in steps"
        :key="index"
        @click="goToStep(index)"
        class="w-2.5 h-2.5 rounded-full transition-all duration-300"
        :class="index === currentStep ? 'bg-white scale-125' : 'bg-white/40'"
      ></button>
    </div>

    <!-- Prev / Next / Play-Pause buttons -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-4">
      <button
        @click="prevStep"
        :disabled="currentStep === 0 || fullMode"
        class="flex items-center gap-2 px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white font-semibold border border-white/30 transition-all duration-200 hover:scale-105 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:scale-100"
        :class="{ 'hidden': fullMode }"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
        </svg>
        Previous
      </button>

      <button
        @click="togglePlayPause"
        class="flex items-center justify-center w-12 h-12 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white border border-white/30 transition-all duration-200 hover:scale-110"
      >
        <svg v-if="videoPaused" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M8 5v14l11-7z"/>
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
        </svg>
      </button>

      <button
        @click="nextStep"
        class="flex items-center gap-2 px-6 py-3 rounded-xl bg-[#7433ed] hover:bg-[#7B4DD3] backdrop-blur-sm text-white font-semibold border border-purple-400/30 transition-all duration-200 hover:scale-105"
        :class="{ 'hidden': fullMode }"
      >
        <template v-if="currentStep === steps.length - 1">
          ↩ Start Over
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582M20 20v-5h-.581M4.582 9A8 8 0 1119.418 15"/>
          </svg>
        </template>
        <template v-else>
          Next
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
          </svg>
        </template>
      </button>
    </div>

    <!-- Buffering indicator -->
    <div v-if="isBuffering" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
      <div class="w-12 h-12 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
    </div>
  </section>
</template>

<script>
import { ref, reactive, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue';

export default {
  name: 'VideoWalkthrough',

  props: {
    videoSrc: {
      type: String,
      required: true
    },
    steps: {
      type: Array,
      default: () => [
        { time: 0,  title: 'Welcome' },
        { time: 2,  title: 'Create a Project' },
        { time: 6,  title: 'Assign Tasks' },
        { time: 10, title: 'Track Progress' },
      ]
    }
  },

  setup(props) {
    // Refs
    const videoRef = ref(null);

    // State
    const currentStep = ref(0);
    const fullMode = ref(false);
    const isSeeking = ref(false);
    const isVideoReady = ref(false);
    const isBuffering = ref(false);
    const videoPaused = ref(true);
    const fadeOverlayOpacity = ref(0);

    // Computed
    const progressWidth = computed(() => {
      const video = videoRef.value;
      if (!video || !isVideoReady.value) return '0%';

      if (fullMode.value) {
        return video.duration ? `${(video.currentTime / video.duration) * 100}%` : '0%';
      }

      const stepStart = props.steps[currentStep.value].time;
      const stepEnd = props.steps[currentStep.value + 1]?.time ?? video.duration;
      const stepLen = stepEnd - stepStart;
      const elapsed = video.currentTime - stepStart;
      return `${Math.min((elapsed / stepLen) * 100, 100)}%`;
    });

    // Methods
    const onVideoReady = () => {
      isVideoReady.value = true;
      if (videoRef.value) {
        videoRef.value.currentTime = 0;
      }
    };

    const onTimeUpdate = () => {
      if (isSeeking.value || !isVideoReady.value) return;

      const video = videoRef.value;
      if (!video) return;

      // Auto-advance to next step
      if (!fullMode.value && currentStep.value < props.steps.length - 1) {
        const nextStepTime = props.steps[currentStep.value + 1].time;
        if (video.currentTime >= nextStepTime - 0.1) {
          currentStep.value++;

          // Pause at the end of each step
          if (currentStep.value < props.steps.length - 1) {
            video.pause();
          }
        }
      }
    };

    const onSeeked = () => {
      if (isSeeking.value) {
        const video = videoRef.value;
        if (video) {
          video.play()
            .then(() => {
              fadeOverlayOpacity.value = 0;
              setTimeout(() => {
                isSeeking.value = false;
              }, 250);
            })
            .catch(error => {
              console.error('Playback failed after seek:', error);
              fadeOverlayOpacity.value = 0;
              isSeeking.value = false;
            });
        }
      }
    };

    const onBuffering = () => {
      isBuffering.value = true;
    };

    const onCanPlay = () => {
      isBuffering.value = false;
    };

    const updatePlayPauseIcon = () => {
      const video = videoRef.value;
      if (video) {
        videoPaused.value = video.paused;
      }
    };

    const goToStep = async (index) => {
      if (isSeeking.value || !isVideoReady.value || !videoRef.value) return;

      // Wrap index if needed
      if (index >= props.steps.length) index = 0;
      if (index < 0) return;

      const targetTime = props.steps[index].time;
      const video = videoRef.value;

      // Don't seek if already at target time
      if (Math.abs(video.currentTime - targetTime) < 0.1) {
        currentStep.value = index;
        return;
      }

      isSeeking.value = true;
      currentStep.value = index;

      // Fade out
      fadeOverlayOpacity.value = 1;

      // Pause current playback
      video.pause();

      // Set new time
      video.currentTime = targetTime;
    };

    const nextStep = () => {
      if (fullMode.value) return;
      const next = currentStep.value < props.steps.length - 1 ? currentStep.value + 1 : 0;
      goToStep(next);
    };

    const prevStep = () => {
      if (fullMode.value || currentStep.value === 0) return;
      goToStep(currentStep.value - 1);
    };

    const togglePlayPause = () => {
      const video = videoRef.value;
      if (!video) return;

      if (video.paused) {
        video.play().catch(error => {
          console.error('Playback failed:', error);
        });
      } else {
        video.pause();
      }
    };

    const toggleMode = () => {
      fullMode.value = !fullMode.value;

      if (!fullMode.value) {
        // Find nearest chapter
        const video = videoRef.value;
        if (video) {
          let nearestStep = 0;
          for (let i = props.steps.length - 1; i >= 0; i--) {
            if (video.currentTime >= props.steps[i].time) {
              nearestStep = i;
              break;
            }
          }

          // Go to nearest step
          goToStep(nearestStep);
        }
      }
    };

    // Intersection Observer for auto-play
    let observer = null;

    const setupIntersectionObserver = () => {
      const section = document.getElementById('See_How_It_Works');
      if (!section) return;

      observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          const video = videoRef.value;
          if (entry.isIntersecting && video && video.paused && currentStep.value === 0 && isVideoReady.value) {
            video.play().catch(() => {});
          }
        });
      }, { threshold: 0.5 });

      observer.observe(section);
    };

    // Lifecycle
    onMounted(() => {
      setupIntersectionObserver();
    });

    onBeforeUnmount(() => {
      if (observer) {
        observer.disconnect();
      }
    });

    // Watch for step changes to update UI if needed
    watch(currentStep, () => {
      // You could add additional logic here if needed
    });

    return {
      videoRef,
      currentStep,
      fullMode,
      videoPaused,
      fadeOverlayOpacity,
      isBuffering,
      progressWidth,
      onVideoReady,
      onTimeUpdate,
      onSeeked,
      onBuffering,
      onCanPlay,
      updatePlayPauseIcon,
      goToStep,
      nextStep,
      prevStep,
      togglePlayPause,
      toggleMode
    };
  }
};
</script>

<style scoped>
.transition-none {
  transition: none;
}

.duration-250 {
  transition-duration: 250ms;
}

/* Add any additional custom styles here */
</style>
