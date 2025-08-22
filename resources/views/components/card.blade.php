<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200']) }}>
  <div class="p-4 sm:p-5">
    {{ $slot }}
  </div>
</div>
