export type TaskStatus = "TODO" | "DOING" | "DONE";
export const ALLOWED_STATUSES: TaskStatus[] = ["TODO", "DOING", "DONE"];

export function isValidStatus(v: unknown): v is TaskStatus {
  return typeof v === "string" && ALLOWED_STATUSES.includes(v as TaskStatus);
}

export function nextStatus(s: TaskStatus): TaskStatus {
  const idx = ALLOWED_STATUSES.indexOf(s);
  return ALLOWED_STATUSES[(idx + 1) % ALLOWED_STATUSES.length];
}
