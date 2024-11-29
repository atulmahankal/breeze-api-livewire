@props(['on'])


<div x-data="{ shown: false, timeout: null, message: '', type: 'success' }" x-init="@this.on('{{ $on }}', (eventData) => {
    clearTimeout(timeout);
    message = eventData[0].message || 'Action completed.';
    type = eventData[0].type || 'success';
    shown = true;
    timeout = setTimeout(() => { shown = false }, 5000);
})" x-show.transition.out.opacity.duration.1500ms="shown" x-bind:class="{
    'text-green-800 bg-green-50 dark:bg-gray-800 dark:text-green-400': type === 'success',
    'text-red-800 bg-red-50 dark:bg-gray-800 dark:text-red-400': type === 'error',
    'text-yellow-800 bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300': type === 'warning',
}" x-transition:leave.opacity.duration.1500ms style="display: none;" {{ $attributes->merge(['class' => 'text-sm p-4
    mb-4 rounded-lg w-full']) }}>
    <span x-text="message"></span>
</div>
