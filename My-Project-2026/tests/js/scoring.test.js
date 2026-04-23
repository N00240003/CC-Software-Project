/**
 * These tests verify the resolver type calculation logic.
 * We extract the scoring function from the game so it can
 * be tested independently of SugarCube.
 */

// Mirror of the calculateResolverType logic from SaveGameController
function calculateResolverType(vars) {
    const scores = {
        Collaborator: vars.score_collaborator ?? 0,
        Compromiser: vars.score_compromiser ?? 0,
        Avoider: vars.score_avoider ?? 0,
        Competitor: vars.score_competitor ?? 0,
        Accommodator: vars.score_accommodator ?? 0,
    };

    return Object.entries(scores).sort((a, b) => b[1] - a[1])[0][0];
}

// Mirror of the saveToLaravel payload builder
function buildSavePayload(slot, passageName, variables) {
    return {
        slot: slot,
        chapter: passageName,
        game_variables: {
            score_collaborator: variables.score_collaborator ?? 0,
            score_compromiser: variables.score_compromiser ?? 0,
            score_avoider: variables.score_avoider ?? 0,
            score_competitor: variables.score_competitor ?? 0,
            score_accommodator: variables.score_accommodator ?? 0,
            choice_chapter1: variables.choice_chapter1 ?? "",
            choice_chapter2: variables.choice_chapter2 ?? "",
        },
    };
}

// ── Resolver type tests ───────────────────────────────────────────────

test("returns Collaborator when collaboration score is highest", () => {
    const vars = {
        score_collaborator: 8,
        score_compromiser: 3,
        score_avoider: 1,
        score_competitor: 2,
        score_accommodator: 1,
    };
    expect(calculateResolverType(vars)).toBe("Collaborator");
});

test("returns Avoider when avoidance score is highest", () => {
    const vars = {
        score_collaborator: 1,
        score_compromiser: 2,
        score_avoider: 7,
        score_competitor: 1,
        score_accommodator: 0,
    };
    expect(calculateResolverType(vars)).toBe("Avoider");
});

test("returns Competitor when competition score is highest", () => {
    const vars = {
        score_collaborator: 0,
        score_compromiser: 1,
        score_avoider: 2,
        score_competitor: 9,
        score_accommodator: 3,
    };
    expect(calculateResolverType(vars)).toBe("Competitor");
});

test("handles missing scores by defaulting to 0", () => {
    // Only one score provided — should still return it as the winner
    const vars = { score_collaborator: 3 };
    expect(calculateResolverType(vars)).toBe("Collaborator");
});

test("handles all zero scores without crashing", () => {
    const vars = {
        score_collaborator: 0,
        score_compromiser: 0,
        score_avoider: 0,
        score_competitor: 0,
        score_accommodator: 0,
    };
    // Should return something without throwing
    expect(typeof calculateResolverType(vars)).toBe("string");
});

// ── Save payload tests ────────────────────────────────────────────────

test("buildSavePayload includes correct slot number", () => {
    const payload = buildSavePayload(3, "Chapter1_Start", {
        score_collaborator: 2,
    });
    expect(payload.slot).toBe(3);
});

test("buildSavePayload includes current passage name as chapter", () => {
    const payload = buildSavePayload(1, "Chapter2_Reflection", {});
    expect(payload.chapter).toBe("Chapter2_Reflection");
});

test("buildSavePayload defaults missing scores to 0", () => {
    const payload = buildSavePayload(1, "Start", {});
    expect(payload.game_variables.score_collaborator).toBe(0);
    expect(payload.game_variables.score_avoider).toBe(0);
});

test("buildSavePayload includes chapter choices", () => {
    const payload = buildSavePayload(1, "Start", {
        choice_chapter1: "Listened to both sides",
        choice_chapter2: "Proposed 50/50 split",
    });
    expect(payload.game_variables.choice_chapter1).toBe(
        "Listened to both sides",
    );
    expect(payload.game_variables.choice_chapter2).toBe("Proposed 50/50 split");
});
