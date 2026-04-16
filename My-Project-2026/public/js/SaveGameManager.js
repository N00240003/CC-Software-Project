class SaveGameManager {
    constructor(frameId, csrfToken) {
        this.frame = document.getElementById(frameId);
        this.csrfToken = csrfToken;
        this.init();
    }

    init() {
        this.loadSlots();
        this.listenForIframeMessages();
        this.bindMuteButton();
    }

    async loadSlots() {
        const res = await fetch("/save-game");
        const slotsArray = await res.json();

        // ✅ FIX: map array → slot index
        const slots = {};
        slotsArray.forEach((s) => (slots[s.slot] = s));

        const container = document.getElementById("save-slots");
        container.innerHTML = "";

        for (let i = 1; i <= 5; i++) {
            const save = slots[i];
            const div = document.createElement("div");
            div.className = "border rounded p-3 text-sm";

            if (save) {
                div.innerHTML = `
                    <div class="font-semibold">Slot ${i}</div>
                    <div class="text-gray-500">${save.chapter ?? "Saved Game"}</div>
                    <div class="text-gray-400 text-xs">${new Date(save.updated_at).toLocaleDateString()}</div>

                    <button onclick="saveManager.triggerSave(${i})"
                        class="mt-2 w-full bg-blue-500 text-white rounded px-2 py-1 text-xs">
                        Overwrite
                    </button>

                    <button onclick="saveManager.deleteSlot(${save.id})"
                        class="mt-1 w-full bg-red-400 text-white rounded px-2 py-1 text-xs">
                        Delete
                    </button>

                    <button onclick="saveManager.loadSlot(${i})"
                        class="mt-1 w-full bg-green-500 text-white rounded px-2 py-1 text-xs">
                        Load
                    </button>
                `;
            } else {
                div.innerHTML = `
                    <div class="font-semibold">Slot ${i}</div>
                    <div class="text-gray-400 italic text-xs">Empty</div>

                    <button onclick="saveManager.triggerSave(${i})"
                        class="mt-2 w-full bg-blue-500 text-white rounded px-2 py-1 text-xs">
                        Save Here
                    </button>
                `;
            }

            container.appendChild(div);
        }
    }

    triggerSave(slot) {
        this.frame.contentWindow.postMessage(
            {
                type: "TRIGGER_SAVE",
                slot: slot,
            },
            "*",
        );
    }

    async loadSlot(slot) {
        const res = await fetch("/save-game");
        const slotsArray = await res.json();

        const slots = {};
        slotsArray.forEach((s) => (slots[s.slot] = s));

        const save = slots[slot];

        if (save) {
            this.frame.contentWindow.postMessage(
                {
                    type: "LOAD_GAME",
                    save: save,
                },
                "*",
            );
        }
    }

    async deleteSlot(id) {
        if (!confirm("Delete this save?")) return;

        await fetch(`/save-game/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": this.csrfToken,
                "Content-Type": "application/json",
            },
        });

        this.loadSlots();
    }

    listenForIframeMessages() {
        window.addEventListener("message", async (event) => {
            console.log("Message received:", event.data); // ✅ debug

            if (event.data.type === "SAVE_GAME") {
                await fetch("/save-game", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": this.csrfToken,
                    },
                    body: JSON.stringify(event.data.payload),
                });

                this.frame.contentWindow.postMessage(
                    {
                        type: "SAVE_CONFIRMED",
                        slot: event.data.payload.slot,
                    },
                    "*",
                );
                console.log("Message received:", event.data);

                this.loadSlots();
            }
        });
    }

    bindMuteButton() {
        document.getElementById("mute-btn").addEventListener("click", (e) => {
            this.frame.contentWindow.postMessage({ type: "TOGGLE_MUTE" }, "*");

            e.target.textContent = e.target.textContent.includes("Mute")
                ? "🔇 Unmute Audio"
                : "🔊 Mute Audio";
        });
    }
}
