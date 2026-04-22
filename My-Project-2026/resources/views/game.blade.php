<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-[#5B53ED] leading-tight">
            {{ __('Game!') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 space-y-6">

        {{-- Media Banner --}}
        <div class="w-full bg-[#D9D7FF] shadow-lg overflow-hidden">
            <img src="/images/banner.jpg" alt="Game Banner" class="w-full h-64 object-cover">
        </div>

        {{-- Game + Controls Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            {{-- iFrame (takes 3/4 width) --}}
            <div class="lg:col-span-3 bg-white shadow-lg rounded-2xl overflow-hidden">
                <iframe id="tweego-frame" src="/stories/conflict-fighter/output/index.html"
                    class="w-full h-[600px] border-0"></iframe>
            </div>

            {{-- Controls (side panel) --}}
            <div class="bg-[#D9D7FF] shadow-lg rounded-2xl p-6 flex flex-col gap-4">

                <h3 class="text-lg font-semibold text-[#5B53ED]">
                    Controls
                </h3>

                <button id="save-btn" onclick="saveManager.promptSaveSlot()"
                    class="w-full px-4 py-3 bg-[#6E68DE] text-white rounded-xl hover:bg-[#5B53ED] transition font-semibold">
                    💾 Save Game
                </button>

                <button id="mute-btn"
                    class="w-full px-4 py-3 bg-[#827ECF] text-white rounded-xl hover:bg-[#7470B8] transition font-semibold">
                    🔊 Mute Audio
                </button>

                <div class="text-xs text-[#7470B8] mt-auto pt-4 border-t border-[#817AFF]">
                    Tip: Your choices affect your conflict style score.
                </div>

            </div>
        </div>
    </div>

    {{-- JS Bridge --}}
    <script src="/js/SaveGameManager.js"></script>

    <script>
        const saveManager = new SaveGameManager('tweego-frame', '{{ csrf_token() }}');

        window.addEventListener('load', async function() {
            const saveId = sessionStorage.getItem('loadSaveId');
            if (!saveId) return;

            sessionStorage.removeItem('loadSaveId');

            const res = await fetch('/save-game');
            const slots = await res.json();

            const save = Object.values(slots).find(s => s && s.id == saveId);

            if (save) {
                const frame = document.getElementById('tweego-frame');

                frame.addEventListener('load', function() {
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
