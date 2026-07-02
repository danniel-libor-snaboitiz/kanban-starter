import { auth } from "@/auth";

export default auth((req) => {
  const isLoggedIn = !!req.auth;
  const publicPaths = ["/", "/login", "/register", "/api/register", "/api/auth"];
  const isPublic = publicPaths.some((p) => req.nextUrl.pathname === p || req.nextUrl.pathname.startsWith(p + "/"));
  if (!isLoggedIn && !isPublic) {
    const url = new URL("/login", req.url);
    return Response.redirect(url);
  }
});

export const config = {
  matcher: ["/((?!_next/static|_next/image|favicon.ico).*)"],
};
