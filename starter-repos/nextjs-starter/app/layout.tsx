import "./globals.css";
import type { Metadata } from "next";

export const metadata: Metadata = { title: "Task Starter", description: "Starter for Claude Code training" };

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en">
      <body className="min-h-screen bg-gray-50 text-gray-900">
        <nav className="bg-white border-b border-gray-200 px-6 py-3 flex justify-between">
          <a href="/tasks" className="font-semibold">Tasks</a>
          <div className="space-x-4 text-sm">
            <a href="/login" className="text-gray-600 hover:text-gray-900">Login</a>
            <a href="/register" className="text-gray-600 hover:text-gray-900">Register</a>
          </div>
        </nav>
        <main className="p-6">{children}</main>
      </body>
    </html>
  );
}
