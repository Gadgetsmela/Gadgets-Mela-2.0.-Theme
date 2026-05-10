import type { Metadata } from 'next';
import './globals.css';

export const metadata: Metadata = {
  title: 'Gadgets Mela | Premium Gadget Deals',
  description: 'Premium public storefront for Gadgets Mela product deals powered by WordPress.'
};

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="en">
      <body>{children}</body>
    </html>
  );
}
