<script setup>

</script>

<template>
<div
  class="w-full bg-[#ffffff] dark:bg-[#222321]
         rounded-lg shadow-md
         p-4 sm:p-6
         min-h-[18rem] sm:min-h-[20rem]
         flex flex-col gap-4 overflow-hidden
         animate-pulse">
    <div  class="animate-pulse taskDetailSkeleton">
    <!-- Status Badge Skeleton -->
    <div class="flex items-center gap-2 sm:gap-3 flex-wrap mb-3 sm:mb-4">
        <div class="w-5 h-5 sm:w-6 sm:h-6 bg-gray-300 rounded-full"></div>
        <div class="h-6 sm:h-7 w-20 sm:w-24 bg-gray-300 rounded-full"></div>
    </div>

    <!-- Title Skeleton -->
    <div class="h-8 sm:h-9 bg-gray-300 rounded-lg mb-3 sm:mb-4 w-full sm:w-3/4"></div>

    <!-- Description Skeleton -->
    <div class="space-y-2 mb-4 sm:mb-6">
        <div class="h-5 sm:h-6 bg-gray-200 rounded w-full"></div>
        <div class="h-5 sm:h-6 bg-gray-200 rounded w-5/6"></div>
        <div class="h-5 sm:h-6 bg-gray-200 rounded w-4/6"></div>
    </div>

    <!-- Info Cards Skeleton -->
    <div class="flex flex-col sm:flex-row gap-3 mb-4 sm:mb-6">
        <div class="bg-gray-100 rounded-lg p-3 sm:p-4 flex-1">
            <div class="h-5 sm:h-6 bg-gray-300 rounded w-28 sm:w-32 mb-2 sm:mb-3"></div>
            <div class="space-y-2 sm:space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                    <div class="h-4 sm:h-5 bg-gray-200 rounded w-32 sm:w-40"></div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                    <div class="h-4 sm:h-5 bg-gray-200 rounded w-28 sm:w-36"></div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                    <div class="h-4 sm:h-5 bg-gray-200 rounded w-28 sm:w-36"></div>
                </div>
            </div>
        </div>

        <div class="bg-gray-100 rounded-lg p-3 sm:p-4 flex-1">
            <div class="h-5 sm:h-6 bg-gray-300 rounded w-16 sm:w-20 mb-2 sm:mb-3"></div>
            <div class="space-y-2 sm:space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                    <div class="h-4 sm:h-5 bg-gray-200 rounded w-32 sm:w-40"></div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                    <div class="h-4 sm:h-5 bg-gray-200 rounded w-28 sm:w-36"></div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                    <div class="h-4 sm:h-5 bg-gray-200 rounded w-28 sm:w-36"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Buttons Skeleton -->
    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
        <div class="h-8  bg-gray-300 rounded-lg w-full sm:w-40"></div>
        <div class="h-8  bg-gray-200 rounded-lg w-full sm:w-32"></div>
    </div>
</div>
</div>

</template>
