<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Game!') }}
        </h2>
    </x-slot>

    {{-- Block 1: Media --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __('Media block') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Block 2: iFrame --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <iframe id="tweego-frame" src="/stories/conflict-fighter/output/index.html" width="100%"
                        height="600px" frameborder="0">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    {{-- Block 3: Save slots + controls --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h3 class="font-semibold text-lg mb-4">Save Game</h3>

                    {{-- Save slots will be rendered here by JS --}}
                    <div id="save-slots" class="grid grid-cols-5 gap-3 mb-4">
                        <p class="text-gray-400 col-span-5">Loading slots...</p>
                    </div>

                    <button id="mute-btn"
                        class="mt-4 px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300">
                        🔊 Mute Audio
                    </button>

                </div>
            </div>
        </div>
    </div>

    {{-- JS Bridge: must be inside <x-app-layout> --}}
    <script src="/js/SaveGameManager.js"></script>
    <script>
        const saveManager = new SaveGameManager('tweego-frame', '{{ csrf_token() }}');
    </script>

</x-app-layout>
