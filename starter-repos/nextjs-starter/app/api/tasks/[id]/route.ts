import { NextResponse } from "next/server";
import { z } from "zod";
import { auth } from "@/auth";
import { prisma } from "@/lib/prisma";

const updateSchema = z.object({
  title: z.string().min(1).optional(),
  description: z.string().optional(),
  status: z.enum(["TODO", "DOING", "DONE"]).optional(),
});

export async function GET(_: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session?.user) return NextResponse.json({ error: "unauthorized" }, { status: 401 });
  const { id } = await params;
  const userId = (session.user as { id?: string }).id!;
  const task = await prisma.task.findFirst({ where: { id, userId } });
  if (!task) return NextResponse.json({ error: "not found" }, { status: 404 });
  return NextResponse.json(task);
}

export async function PATCH(request: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session?.user) return NextResponse.json({ error: "unauthorized" }, { status: 401 });
  const { id } = await params;
  const userId = (session.user as { id?: string }).id!;
  const body = await request.json();
  const parsed = updateSchema.safeParse(body);
  if (!parsed.success) return NextResponse.json({ error: parsed.error.flatten() }, { status: 400 });
  const task = await prisma.task.findFirst({ where: { id, userId } });
  if (!task) return NextResponse.json({ error: "not found" }, { status: 404 });
  const updated = await prisma.task.update({ where: { id }, data: parsed.data });
  return NextResponse.json(updated);
}

export async function DELETE(_: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session?.user) return NextResponse.json({ error: "unauthorized" }, { status: 401 });
  const { id } = await params;
  const userId = (session.user as { id?: string }).id!;
  const task = await prisma.task.findFirst({ where: { id, userId } });
  if (!task) return NextResponse.json({ error: "not found" }, { status: 404 });
  await prisma.task.delete({ where: { id } });
  return new NextResponse(null, { status: 204 });
}
