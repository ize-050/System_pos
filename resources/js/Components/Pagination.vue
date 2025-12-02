<template>
  <div v-if="links.length > 0" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
    <div class="flex flex-1 justify-between sm:hidden">
      <Link
        v-if="links[0]?.url"
        :href="links[0].url"
        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
      >
        Previous
      </Link>
      <Link
        v-if="links[links.length - 1]?.url"
        :href="links[links.length - 1].url"
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
      >
        Next
      </Link>
    </div>

    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing
          <span class="font-medium">{{ from }}</span>
          to
          <span class="font-medium">{{ to }}</span>
          of
          <span class="font-medium">{{ total }}</span>
          results
        </p>
      </div>

      <div>
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          <template v-for="(link, index) in links" :key="index">
            <Link
              v-if="link.url"
              :href="link.url"
              :class="[
                'relative inline-flex items-center px-4 py-2 text-sm font-semibold',
                link.active
                  ? 'z-10 bg-primary-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600'
                  : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0',
                index === 0 ? 'rounded-l-md' : '',
                index === links.length - 1 ? 'rounded-r-md' : '',
                'cursor-pointer'
              ]"
              v-html="formatLabel(link.label)"
            ></Link>
            <span
              v-else
              :class="[
                'relative inline-flex items-center px-4 py-2 text-sm font-semibold',
                'text-gray-400 ring-1 ring-inset ring-gray-200 bg-gray-100',
                index === 0 ? 'rounded-l-md' : '',
                index === links.length - 1 ? 'rounded-r-md' : '',
                'cursor-not-allowed opacity-60'
              ]"
              v-html="formatLabel(link.label)"
            ></span>
          </template>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  links: {
    type: Array,
    required: true,
  },
  from: {
    type: Number,
    required: true,
  },
  to: {
    type: Number,
    required: true,
  },
  total: {
    type: Number,
    required: true,
  },
})

const formatLabel = (label) => {
  if (label === null || label === undefined) {
    return ''
  }

  if (typeof label === 'string') {
    return label
  }

  try {
    return String(label)
  } catch (error) {
    return ''
  }
}
</script>