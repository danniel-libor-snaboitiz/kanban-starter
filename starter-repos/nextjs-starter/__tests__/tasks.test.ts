import { isValidStatus, nextStatus, ALLOWED_STATUSES } from "../lib/status";

describe("status helpers", () => {
  test("ALLOWED_STATUSES has three entries", () => {
    expect(ALLOWED_STATUSES).toEqual(["TODO", "DOING", "DONE"]);
  });

  test("isValidStatus accepts valid statuses", () => {
    expect(isValidStatus("TODO")).toBe(true);
    expect(isValidStatus("DOING")).toBe(true);
    expect(isValidStatus("DONE")).toBe(true);
  });

  test("isValidStatus rejects invalid values", () => {
    expect(isValidStatus("todo")).toBe(false);
    expect(isValidStatus("")).toBe(false);
    expect(isValidStatus(42)).toBe(false);
    expect(isValidStatus(null)).toBe(false);
  });

  test("nextStatus cycles TODO -> DOING -> DONE -> TODO", () => {
    expect(nextStatus("TODO")).toBe("DOING");
    expect(nextStatus("DOING")).toBe("DONE");
    expect(nextStatus("DONE")).toBe("TODO");
  });
});
