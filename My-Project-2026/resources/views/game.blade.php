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
                        height="500px" frameborder="0">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    {{-- Block 3: Save + Mute controls --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex gap-4 items-center">

                    <button id="save-btn" onclick="saveManager.promptSaveSlot()"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        💾 Save Game
                    </button>

                    <button id="mute-btn" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300">
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
        // Check if we arrived from saves page with a save to load
        window.addEventListener('load', async function() {
            const saveId = sessionStorage.getItem('loadSaveId');
            if (!saveId) return;

            sessionStorage.removeItem('loadSaveId'); // clear it

            const res = await fetch('/save-game');
            const slots = await res.json();

            // Find the save by id across all slots
            const save = Object.values(slots).find(s => s && s.id == saveId);
            if (save) {
                const frame = document.getElementById('tweego-frame');
                frame.addEventListener('load', function() {
                    // Small delay to make sure SugarCube is fully initialised
                    setTimeout(function() {
                        frame.contentWindow.postMessage({
                            type: 'LOAD_GAME',
                            save: save
                        }, '*');
                    }, 500);
                });
            }
        });
    </script>

</x-app-layout>
