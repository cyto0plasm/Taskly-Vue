<template>
  <div
    id="container"
    class="auth-container"
    :class="{ mobile: isMobile }"
  >
    <!-- Login Form -->
    <form
      id="login-form"
      method="POST"
      :action="config.loginRoute"
      class="auth-form"
      :style="loginFormStyle"
    >
      <input type="hidden" name="_token" :value="config.csrfToken" />

      <h1 class="form-title">Sign In</h1>

      <!-- Social -->
      <div class="social-section">
        <div class="social-icons">
          <a :href="config.googleRoute" class="social-btn" title="Sign in with Google">
            <svg width="22" height="22" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
          </a>
          <a :href="config.githubRoute" class="social-btn" title="Sign in with GitHub">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/>
            </svg>
          </a>
        </div>
        <div class="divider"><span>or sign in with email</span></div>
      </div>

      <!-- Fields -->
      <div class="field-group">
        <div class="field">
          <label for="email-login">Email</label>
          <input id="email-login" type="email" name="email" placeholder="Enter your email..." autocomplete="email" />
          <span v-if="errors.login?.email" class="field-error">{{ errors.login.email }}</span>
        </div>
        <div class="field">
          <label for="password-login">Password</label>
          <input id="password-login" type="password" name="password" placeholder="Enter your password..." autocomplete="current-password" />
          <span v-if="errors.login?.password" class="field-error">{{ errors.login.password }}</span>
        </div>
      </div>

      <label class="remember-label">
        <input type="checkbox" name="remember" />
        <span>Remember me</span>
      </label>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Sign In</button>
        <a v-if="config.forgotPasswordRoute" :href="config.forgotPasswordRoute" class="forgot-link">
          Forgot your password?
        </a>
      </div>

      <!-- Mobile only switch -->
      <button type="button" class="mobile-switch" @click="toggle">
        Don't have an account? <strong>Sign Up →</strong>
      </button>
    </form>

    <!-- Register Form -->
    <form
      id="register-form"
      method="POST"
      :action="config.registerRoute"
      class="auth-form"
      :style="registerFormStyle"
    >
      <input type="hidden" name="_token" :value="config.csrfToken" />

      <h1 class="form-title">Sign Up</h1>

      <div class="field-group">
        <div class="field">
          <label for="name-register">Username</label>
          <input id="name-register" type="text" name="name" placeholder="Enter your username..." autocomplete="username" />
          <span v-if="errors.register?.name" class="field-error">{{ errors.register.name }}</span>
        </div>
        <div class="field">
          <label for="email-register">Email</label>
          <input id="email-register" type="email" name="email" placeholder="Enter your email..." autocomplete="email" />
          <span v-if="errors.register?.email" class="field-error">{{ errors.register.email }}</span>
        </div>
        <div class="field">
          <label for="password-register">Password</label>
          <input id="password-register" type="password" name="password" placeholder="Enter your password..." autocomplete="new-password" />
          <span v-if="errors.register?.password" class="field-error">{{ errors.register.password }}</span>
        </div>
        <div class="field">
          <label for="confirm-password-register">Confirm Password</label>
          <input id="confirm-password-register" type="password" name="password_confirmation" placeholder="Confirm your password..." autocomplete="new-password" />
          <span v-if="errors.register?.password_confirmation" class="field-error">{{ errors.register.password_confirmation }}</span>
        </div>
      </div>

      <button type="submit" class="btn btn-success">Sign Up</button>

      <!-- Mobile only switch -->
      <button type="button" class="mobile-switch" @click="toggle">
        Already have an account? <strong>← Sign In</strong>
      </button>
    </form>

    <!-- Sliding Message Panel -->
    <div id="message" :style="panelStyle" aria-live="polite">
      <div class="panel-blob panel-blob-top"></div>
      <div class="panel-blob panel-blob-bottom"></div>

      <img :src="config.logoUrl" class="panel-logo" alt="Taskly" />
      <h2 class="panel-title">{{ panelContent.title }}</h2>
      <p class="panel-text">{{ panelContent.text }}</p>
      <button class="panel-btn" @click="toggle">{{ panelContent.button }}</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

// ── Config from window (set by Blade) ──────────────────────────────────────
const config = window.__AUTH_CONFIG__ || {}

// ── Errors from window (set by Blade) ─────────────────────────────────────
const errors = window.__AUTH_ERRORS__ || {}

// ── State ──────────────────────────────────────────────────────────────────
const windowWidth = ref(window.innerWidth)
const isMobile = computed(() => windowWidth.value <= 768)

const savedMode = localStorage.getItem('isLogin')
const defaultIsLogin = config.defaultForm === 'register'
  ? false
  : savedMode === null ? true : savedMode === 'true'

const isLogin = ref(defaultIsLogin)

// ── Panel copy ─────────────────────────────────────────────────────────────
const panelContent = computed(() => isLogin.value
  ? {
      title: 'Welcome Back!',
      text: 'Manage your tasks, track progress, and stay productive. Sign in to continue where you left off.',
      button: '← Sign Up',
    }
  : {
      title: 'Start Organizing Today!',
      text: 'Create an account and take control of your tasks. Stay on top of deadlines and boost productivity.',
      button: 'Sign In →',
    }
)

// ── Toggle ─────────────────────────────────────────────────────────────────
function toggle() {
  isLogin.value = !isLogin.value
  localStorage.setItem('isLogin', JSON.stringify(isLogin.value))
}

// ── Computed styles ────────────────────────────────────────────────────────
const loginFormStyle = computed(() => ({
  opacity: isLogin.value ? '1' : '0',
  pointerEvents: isLogin.value ? 'auto' : 'none',
  transform: isMobile.value
    ? isLogin.value ? 'translateY(0)' : 'translateY(-100%)'
    : isLogin.value ? 'translateX(0)' : 'translateX(-100%)',
}))

const registerFormStyle = computed(() => ({
  opacity: isLogin.value ? '0' : '1',
  pointerEvents: isLogin.value ? 'none' : 'auto',
  transform: isMobile.value
    ? isLogin.value ? 'translateY(100%)' : 'translateY(0)'
    : isLogin.value ? 'translateX(100%)' : 'translateX(0)',
}))

const panelStyle = computed(() => {
  const bg = isLogin.value
    ? 'linear-gradient(to left, #4f46e5, #3b82f6)'
    : 'linear-gradient(to right, #16a34a, #22c55e)'

  const transform = isMobile.value
    ? isLogin.value ? 'translateY(0)' : 'translateY(100%)'
    : isLogin.value ? 'translateX(100%)' : 'translateX(0)'

  const borderRadius = isMobile.value
    ? isLogin.value ? '1.5rem 1.5rem 0 0' : '0 0 1.5rem 1.5rem'
    : isLogin.value ? '3rem 0 0 6rem' : '0 3rem 6rem 0'

  return { backgroundImage: bg, transform, borderRadius }
})

// ── Resize handler ─────────────────────────────────────────────────────────
let resizeTimer = null
function onResize() {
  clearTimeout(resizeTimer)
  resizeTimer = setTimeout(() => { windowWidth.value = window.innerWidth }, 150)
}

onMounted(() => window.addEventListener('resize', onResize))
onUnmounted(() => window.removeEventListener('resize', onResize))
</script>

<style scoped>
.auth-container {
  display: flex;
  width: min(980px, 80rem);
  height: 600px;
  background: #f3f4f6;
  border-radius: 1.5rem;
  overflow: hidden;
  position: relative;
}

/* ── Forms ── */
.auth-form {
  width: 50%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 1.25rem;
  padding: 2rem;
  transition: transform 0.7s ease, opacity 0.5s ease;
  position: relative;
  z-index: 20;
}

.form-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 0.25rem;
}

/* ── Social ── */
.social-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  width: 100%;
  max-width: 22rem;
}
.social-icons {
  display: flex;
  gap: 0.75rem;
}
.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 1px 4px rgba(0,0,0,0.15);
  color: #374151;
  transition: box-shadow 0.2s, background 0.2s;
}
.social-btn:hover { background: #e5e7eb; box-shadow: 0 3px 8px rgba(0,0,0,0.15); }

.divider {
  display: flex;
  align-items: center;
  width: 100%;
  gap: 0.5rem;
  color: #9ca3af;
  font-size: 0.8rem;
}
.divider::before, .divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #d1d5db;
}

/* ── Fields ── */
.field-group {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  width: 100%;
  max-width: 22rem;
}
.field {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}
.field label {
  font-size: 0.8rem;
  font-weight: 600;
  color: #374151;
}
.field input {
  padding: 0.55rem 0.85rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  background: #fff;
  color: #111827;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.field input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
}
.field-error {
  font-size: 0.75rem;
  color: #ef4444;
}

/* ── Remember ── */
.remember-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.85rem;
  color: #4b5563;
  align-self: flex-start;
  margin-left: 0.5rem;
  cursor: pointer;
  user-select: none;
}

/* ── Buttons ── */
.form-actions {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  width: 100%;
  max-width: 22rem;
}
.btn {
  padding: 0.6rem 2rem;
  border-radius: 0.6rem;
  font-weight: 600;
  font-size: 0.9rem;
  border: none;
  cursor: pointer;
  transition: background 0.15s, transform 0.1s;
  width: 50%;
}
.btn:active { transform: scale(0.97); }
.btn-primary { background: #4f46e5; color: #fff; }
.btn-primary:hover { background: #4338ca; }
.btn-success { background: #10b981; color: #fff; }
.btn-success:hover { background: #059669; }
.btn-success:active { background: #047857; }

.forgot-link {
  font-size: 0.8rem;
  color: #6b7280;
  text-decoration: underline;
  text-underline-offset: 2px;
}
.forgot-link:hover { color: #111827; }

/* ── Mobile switch link ── */
.mobile-switch {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 0.85rem;
  color: #4b5563;
  margin-top: 0.5rem;
}
.mobile-switch strong { color: #4f46e5; }

/* ── Sliding panel ── */
#message {
  position: absolute;
  width: 50%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 2.5rem;
  z-index: 10;
  color: #fff;
  transition: transform 0.7s ease, border-radius 0.7s ease, background-image 0.5s ease;
  overflow: hidden;
}
.panel-blob {
  position: absolute;
  background: rgba(255,255,255,0.1);
  border-radius: 50%;
}
.panel-blob-top { width: 120px; height: 120px; top: -40px; left: -40px; }
.panel-blob-bottom { width: 150px; height: 150px; bottom: -60px; right: -60px; }

.panel-logo {
  width: 160px;
  height: auto;
  margin-bottom: 1.5rem;
  object-fit: contain;
}
.panel-title {
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
  text-align: center;
}
.panel-text {
  font-size: 0.9rem;
  font-weight: 300;
  text-align: center;
  margin: 0 1rem 1.5rem;
  line-height: 1.6;
  opacity: 0.9;
}
.panel-btn {
  background: rgba(255,255,255,0.15);
  border: none;
  color: #fff;
  padding: 0.45rem 1.25rem;
  border-radius: 0.5rem;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: background 0.15s;
}
.panel-btn:hover { background: rgba(255,255,255,0.3); }

/* ── Mobile ── */
@media (max-width: 768px) {
  .auth-container {
    flex-direction: column;
    height: 600px;
    width: 100%;
  }

  .auth-form {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    border-radius: 1.5rem;
    overflow-y: auto;
    padding: 1.5rem;
    gap: 0.85rem;
  }

  #message {
    width: 100%;
    height: 50%;
    bottom: 0;
    top: auto;
    position: absolute;
    border-radius: 1.5rem 1.5rem 0 0;
  }

  .mobile-switch {
    display: block;
  }

  .form-title { font-size: 1.4rem; }
  .social-section { display: none; }
  .field-group { gap: 0.5rem; }
}
</style>
