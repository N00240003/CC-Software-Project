/**
 * Tests for the postMessage bridge between SugarCube and Laravel.
 * We mock window.parent.postMessage to verify the right messages
 * are sent without needing a real browser or iframe.
 */

// Mock window.parent.postMessage
const postMessageMock = jest.fn();
global.window = {
    parent: { postMessage: postMessageMock },
    addEventListener: jest.fn(),
    location: { origin: "http://localhost:8000" },
};

// Minimal SugarCube State mock
global.State = {
    variables: {
        score_collaborator: 5,
        score_compromiser: 2,
        score_avoider: 1,
        score_competitor: 0,
        score_accommodator: 3,
        choice_chapter1: "Listened to both sides",
        choice_chapter2: "",
        playerName: "Test Player",
        currentChapter: 1,
        saveChapter: "Chapter1_End",
    },
};

// Minimal passage() mock — returns current passage name
global.passage = () => "Chapter1_End";

// Load the saveToLaravel function
function saveToLaravel(slot) {
    const payload = {
        slot: slot,
        chapter: passage(),
        game_variables: {
            score_collaborator: State.variables.score_collaborator,
            score_compromiser: State.variables.score_compromiser,
            score_avoider: State.variables.score_avoider,
            score_competitor: State.variables.score_competitor,
            score_accommodator: State.variables.score_accommodator,
            choice_chapter1: State.variables.choice_chapter1,
            choice_chapter2: State.variables.choice_chapter2,
        },
    };
    window.parent.postMessage({ type: "SAVE_GAME", payload }, "*");
}

// ── Bridge tests ──────────────────────────────────────────────────────

beforeEach(() => {
    postMessageMock.mockClear();
});

test("saveToLaravel sends a SAVE_GAME message to parent", () => {
    saveToLaravel(1);
    expect(postMessageMock).toHaveBeenCalledTimes(1);

    const [message] = postMessageMock.mock.calls[0];
    expect(message.type).toBe("SAVE_GAME");
});

test("saveToLaravel sends the correct slot number", () => {
    saveToLaravel(3);
    const [message] = postMessageMock.mock.calls[0];
    expect(message.payload.slot).toBe(3);
});

test("saveToLaravel includes current passage as chapter", () => {
    saveToLaravel(1);
    const [message] = postMessageMock.mock.calls[0];
    expect(message.payload.chapter).toBe("Chapter1_End");
});

test("saveToLaravel includes all score variables", () => {
    saveToLaravel(1);
    const [message] = postMessageMock.mock.calls[0];
    const vars = message.payload.game_variables;

    expect(vars.score_collaborator).toBe(5);
    expect(vars.score_compromiser).toBe(2);
    expect(vars.score_avoider).toBe(1);
    expect(vars.score_competitor).toBe(0);
    expect(vars.score_accommodator).toBe(3);
});

test("saveToLaravel includes chapter choices", () => {
    saveToLaravel(1);
    const [message] = postMessageMock.mock.calls[0];
    expect(message.payload.game_variables.choice_chapter1).toBe(
        "Listened to both sides",
    );
});

test("saveToLaravel sends to wildcard origin", () => {
    saveToLaravel(1);
    const [, origin] = postMessageMock.mock.calls[0];
    expect(origin).toBe("*");
});
