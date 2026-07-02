export default function Home() {
  return (
    <div className="max-w-xl mx-auto text-center py-12">
      <h1 className="text-3xl font-bold mb-4">Task Starter</h1>
      <p className="text-gray-600 mb-6">A minimal starter for Claude Code training assignments.</p>
      <div className="space-x-4">
        <a className="inline-block px-4 py-2 bg-black text-white rounded" href="/register">Register</a>
        <a className="inline-block px-4 py-2 border border-gray-300 rounded" href="/login">Login</a>
      </div>
    </div>
  );
}
