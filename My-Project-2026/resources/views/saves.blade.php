<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Saves') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                    Save Slots
                </h3>
                <div id="save-slots" class="grid grid-cols-5 gap-3">
                    <p class="text-gray-400 col-span-5">Loading slots...</p>
                </div>
            </div>
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
                div.className = 'border rounded p-3 text-sm dark:border-gray-700';

                if (save) {
                    div.innerHTML = `
                    <div class="font-semibold text-gray-800 dark:text-gray-100">Slot ${i}</div>
                    <div class="text-gray-500 text-xs">${save.chapter}</div>
                    <div class="text-gray-400 text-xs">${new Date(save.updated_at).toLocaleDateString()}</div>
                    <button onclick="loadSave(${save.id}, '${save.chapter}')"
                        class="mt-2 w-full bg-green-500 text-white rounded px-2 py-1 text-xs hover:bg-green-600">
                        Load
                    </button>
                    <button onclick="deleteSlot(${save.id})"
                        class="mt-1 w-full bg-red-400 text-white rounded px-2 py-1 text-xs hover:bg-red-500">
                        Delete
                    </button>
                `;
                } else {
                    div.innerHTML = `
                    <div class="font-semibold text-gray-800 dark:text-gray-100">Slot ${i}</div>
                    <div class="text-gray-400 italic text-xs">Empty</div>
                `;
                }

                container.appendChild(div);
            }
        }

        function loadSave(id, chapter) {
            // Store the save ID in sessionStorage then redirect to game page
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
