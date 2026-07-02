"use client";
import { useState } from "react";

export default function TaskForm() {
  const [title, setTitle] = useState("");
  const [description, setDescription] = useState("");
  async function submit(e: React.FormEvent) {
    e.preventDefault();
    const res = await fetch("/api/tasks", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ title, description }),
    });
    if (res.ok) { setTitle(""); setDescription(""); window.location.reload(); }
  }
  return (
    <form onSubmit={submit} className="bg-white border rounded p-4 space-y-2">
      <input className="w-full border rounded px-3 py-2" placeholder="Task title" required value={title} onChange={(e) => setTitle(e.target.value)} />
      <textarea className="w-full border rounded px-3 py-2" placeholder="Description (optional)" value={description} onChange={(e) => setDescription(e.target.value)} />
      <button className="bg-black text-white rounded px-3 py-2">Add task</button>
    </form>
  );
}
