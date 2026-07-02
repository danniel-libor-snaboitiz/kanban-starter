import { auth } from "@/auth";
import { prisma } from "@/lib/prisma";
import { redirect } from "next/navigation";
import TaskForm from "./TaskForm";

export default async function TasksPage() {
  const session = await auth();
  if (!session?.user) redirect("/login");
  const userId = (session.user as { id?: string }).id!;
  const tasks = await prisma.task.findMany({ where: { userId }, orderBy: { createdAt: "desc" } });
  return (
    <div className="max-w-3xl mx-auto space-y-6">
      <h1 className="text-2xl font-semibold">My Tasks</h1>
      <TaskForm />
      <ul className="space-y-2">
        {tasks.map((t) => (
          <li key={t.id} className="bg-white border rounded p-3 flex justify-between">
            <div>
              <div className="font-medium">{t.title}</div>
              <div className="text-sm text-gray-600">{t.description ?? ""}</div>
            </div>
            <span className="text-xs uppercase tracking-wide px-2 py-1 bg-gray-100 rounded">{t.status}</span>
          </li>
        ))}
        {tasks.length === 0 && <p className="text-gray-500">No tasks yet.</p>}
      </ul>
    </div>
  );
}
