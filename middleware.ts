import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

const blockedAdminRoutes = ['/admin', '/admin/products', '/admin/analytics'];

export function middleware(request: NextRequest) {
  if (blockedAdminRoutes.some((route) => request.nextUrl.pathname === route || request.nextUrl.pathname.startsWith(`${route}/`))) {
    return new NextResponse('Not Found', { status: 404 });
  }

  return NextResponse.next();
}

export const config = {
  matcher: ['/admin/:path*']
};
