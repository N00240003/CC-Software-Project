class SaveGameManager {
    constructor(frameId, csrfToken) {
        // Whats csrfToken/frameID and why we extracting them?
        //csrfToken is Laravel's security token passed in from Blade
        // —> it's stored so every fetch request can include it in its headers.
        this.frame = document.getElementById(frameId);
        //frameId is the string 'tweego-frame'
        // —> it uses that to find the actual iframe element in the DOM and store it as this.frame
        // so every method can access it without searching the DOM again.
        this.csrfToken = csrfToken;
        this.init(); //Whats this? = Funtion thats calling other functions
    }

    init() {
        // Just a setup method that calls the two things
        // that need to start running immediately when the page loads
        this.listenForIframeMessages();
        this.bindMuteButton();
    }

    promptSaveSlot() {
        //Called when the player clicks the Save Game button
        const slot = prompt("Save to which slot? (1-5)");
        // Prompt will show message + input box
        const num = parseInt(slot);
        if (num >= 1 && num <= 5) {
            this.triggerSave(num);
            // If number is valid slot will be saved
        } else if (slot !== null) {
            alert("Please enter a number between 1 and 5.");
        }
    }

    // Load lsots on page
    async loadSlots() {
        const res = await fetch("/save-game");
        const slots = await res.json();
        const container = document.getElementById("save-slots");
        container.innerHTML = "";
        // ^ Clears whatever is currently rendered to refresh UI
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

    // Sends a message into iframe telling SugarCube to trigger a save
    triggerSave(slot) {
        // frame.cWindow is windo object in iframe
        this.frame.contentWindow.postMessage(
            // postMessage takes two arguments:
            // the message object and the target origin ("*" means any origin)
            // The message itself is just a plain object with a type so SugarCube knows what to do with it
            // and the slot number so it knows which slot to save to.
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
        if (!confirm("Delete this save?")) return;
        await fetch(`/save-game/${id}`, {
            //sends a DELETE request to /save-game/{id} with the CSRF token in the headers
            // (Laravel requires this for any non-GET request as a security measure)
            method: "DELETE",
            headers: { "X-CSRF-TOKEN": this.csrfToken },
        });
        this.loadSlots(); //Refresh UI
    }

    // Listen for SAVE_GAME messaegs from iFrame
    listenForIframeMessages() {
        window.addEventListener("message", async (event) => {
            if (event.origin !== window.location.origin) return;
            // window.addEventListener("message", ...) runs continuously in the background
            // waiting for any postMessage to arrive.
            // The event.origin check is a security measure
            // —> it ignores any messages that don't come from the same domain as the page.
            if (event.data.type === "SAVE_GAME") {
                await fetch("/save-game", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": this.csrfToken,
                    },
                    body: JSON.stringify(event.data.payload),
                });
                // When a SAVE_GAME message arrives it extracts the payload and POSTs it to Laravel
                this.frame.contentWindow.postMessage(
                    { type: "SAVE_CONFIRMED", slot: event.data.payload.slot },
                    "*",
                );
                // After a successful save it sends a SAVE_CONFIRMED message back into the iframe
                // (so SugarCube can show a confirmation alert)

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
