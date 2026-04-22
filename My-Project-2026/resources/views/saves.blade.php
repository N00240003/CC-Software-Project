<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-[#5B53ED] leading-tight">
            {{ __('My Saves') }}
        </h2>
    </x-slot>

    <div class="max-w-6xl mx-auto py-10 space-y-6">

        <h3 class="text-xl font-semibold text-[#5B53ED]">
            Save Slots
        </h3>

        {{-- Save Slots Grid --}}
        <div id="save-slots" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            <p class="text-[#827ECF] col-span-5">Loading slots...</p>
        </div>

    </div>

    <script>
        async function loadSlots() {
            const res = await fetch('/save-game');
            const slots = await res.json();
            const container = document.getElementById('save-slots');
            container.innerHTML = '';

            for (let i = 1; i <= 5; i++) {
                const save = slots[i];
                const div = document.createElement('div');

                div.className = `
                    bg-[#D9D7FF] rounded-2xl shadow-md p-5 flex flex-col justify-between
                    border border-[#817AFF] min-h-[160px]
                `;

                if (save) {
                    div.innerHTML = `
                        <div class="space-y-2">
                            <div class="text-lg font-semibold text-[#5B53ED]">
                                Slot ${i}
                            </div>

                            <div class="text-sm text-[#6E68DE] font-medium">
                                ${save.chapter}
                            </div>

                            <div class="text-xs text-[#827ECF]">
                                ${new Date(save.updated_at).toLocaleDateString()}
                            </div>
                        </div>

                        <div class="mt-4 space-y-2">
                            <button onclick="loadSave(${save.id})"
                                class="w-full bg-[#6E68DE] text-white rounded-lg px-3 py-2 text-sm font-semibold hover:bg-[#5B53ED] transition">
                                Load
                            </button>

                            <button onclick="deleteSlot(${save.id})"
                                class="w-full bg-[#827ECF] text-white rounded-lg px-3 py-2 text-sm hover:bg-[#7470B8] transition">
                                Delete
                            </button>
                        </div>
                    `;
                } else {
                    div.innerHTML = `
                        <div class="flex flex-col justify-between h-full">
                            <div>
                                <div class="text-lg font-semibold text-[#5B53ED]">
                                    Slot ${i}
                                </div>

                                <div class="text-sm text-[#827ECF] italic mt-2">
                                    Empty
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="w-full border border-dashed border-[#817AFF] rounded-lg py-2 text-center text-xs text-[#827ECF]">
                                    No save data
                                </div>
                            </div>
                        </div>
                    `;
                }

                container.appendChild(div);
            }
        }

        function loadSave(id) {
            sessionStorage.setItem('loadSaveId', id);
            window.location.href = '/game';
        }

        async function deleteSlot(id) {
            if (!confirm('Delete this save?')) return;

            await fetch(`/save-game/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            loadSlots();
        }

        loadSlots();
    </script>

</x-app-layout>
