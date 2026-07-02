"use client";
import { useState } from "react";

export default function RegisterPage() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [name, setName] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [ok, setOk] = useState(false);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(null);
    const res = await fetch("/api/register", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password, name }),
    });
    if (!res.ok) {
      const { error } = await res.json();
      setError(typeof error === "string" ? error : "Registration failed");
      return;
    }
    setOk(true);
    window.location.href = "/login";
  }

  return (
    <div className="max-w-md mx-auto bg-white p-6 rounded shadow">
      <h1 className="text-2xl font-semibold mb-4">Register</h1>
      {error && <p className="text-red-600 mb-2">{error}</p>}
      {ok && <p className="text-green-600 mb-2">Account created. Redirecting...</p>}
      <form onSubmit={handleSubmit} className="space-y-3">
        <input className="w-full border rounded px-3 py-2" placeholder="Name" value={name} onChange={(e) => setName(e.target.value)} />
        <input className="w-full border rounded px-3 py-2" type="email" placeholder="Email" required value={email} onChange={(e) => setEmail(e.target.value)} />
        <input className="w-full border rounded px-3 py-2" type="password" placeholder="Password (8+ chars)" required minLength={8} value={password} onChange={(e) => setPassword(e.target.value)} />
        <button className="w-full bg-black text-white rounded py-2">Create account</button>
      </form>
    </div>
  );
}
