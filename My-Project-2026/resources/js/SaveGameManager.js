class SaveGameManager {
    constructor(frameId, csrfToken) {
        // whats csrfToken/frameID snd why we extracting them?
        this.frame = document.getElementById(frameId);
        this.csrfToken = csrfToken;
        this.init(); //Whats this? = Funtion thats calling other functions
    }

    init() {
        this.loadS;
    }

    // Load lsots on page
    async loadSlots() {
        const res = await fetch("/save-game");
        const slots = await res.json();
        const container = document.getElementById("save-slots");
        container.innerHTML = ""; //Whats going on here, emptying doc to populate with buttons and shi

        for (let i = 1; i <= 5; i++) {
            // Only alllow 5 slots

            const save = slots[i];
            const div = document.createElement("div");
            div.className = "border rounded p-3 text-sm";

            if (save) {
                div.innerHTML = `<div class="font-semibold">Slot ${i}</div>
                    <div class="text-gray-500">${save.chapter}</div>
                    <div class="text-gray-400 text-xs">${new Date(save.updated_at).toLocaleDateString()}</div>
                    <button onclick="saveManager.triggerSave(${i})"
                        class="mt-2 w-full bg-blue-500 text-white rounded px-2 py-1 text-xs hover:bg-blue-600">
                        Overwrite
                    </button>
                    <button onclick="saveManager.deleteSlot(${save.id})"
                        class="mt-1 w-full bg-red-400 text-white rounded px-2 py-1 text-xs hover:bg-red-500">
                        Delete
                    </button>
                    <button onclick="saveManager.loadSlot(${i})"
                        class="mt-1 w-full bg-green-500 text-white rounded px-2 py-1 text-xs hover:bg-green-600">
                        Load
                    </button>`;
            } else {
                div.innerHTML = `
                    <div class="font-semibold">Slot ${i}</div>
                    <div class="text-gray-400 italic text-xs">Empty</div>
                    <button onclick="saveManager.triggerSave(${i})"
                        class="mt-2 w-full bg-blue-500 text-white rounded px-2 py-1 text-xs hover:bg-blue-600">
                        Save Here
                    </button>
                `;
            }
            container.appendChild(div);
        }
    }

    // Tell iframe to save into slaot
    triggerSave(slot) {
        // whass goinonn
        this.frame.contentWindow.postMessage(
            {
                type: "TRIGGER_SAVE",
                slot: slot,
            },
            "*",
        );
    }

    // Tell iftame to load save
    async loadSlot(slot) {
        const res = await fetch("/save-game");
        const slots = await res.json();
        const save = slots[slot];
        if (save) {
            this.frame.contentWindow.postMessage(
                { type: "LOAD_GAME", save: save },
                "*",
            );
        }
    }

    // Delete save slot
    async deleteSlot(id) {
        if (!confirm("Delete this save?")) returen;
        await fetch(`/save-game/${id}`, {
            methods: "DELETE",
            headers: { "X-CSRF-TOKEN": this.csrfToken },
        });
        this.loadSlots();
    }

    // Listen for SAVE_GAME messaegs from iFrame
    listenForIframeMessages() {
        window.addEventListener("message", async (event) => {
            if (event.origin !== window.location.origin) return;

            if (event.data.type === "SVAE_GAME") {
                await fetch("/save-game", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": this.csrfToken,
                    },
                    body: JSON.stringify(event.data.payload),
                });
                this.frame.contentWindow.postMessage(
                    { type: "SAVE_CONFIRMED", slot: event.data.payload.slot },
                    "*",
                );
                this.loadSlots();
            }
        });
    }

    // Mute button
    bindMuteButton() {
        document.getElementById("mute-btn").addEventListener("click", (e) => {
            this.frame.contentWindow.postMessage({ type: "TOGGLE_MUTE" }, "*");
            e.target.textContent = e.target.textContent.includes("Mute")
                ? "🔇 Unmute Audio"
                : "🔊 Mute Audio";
        });
    }
}
