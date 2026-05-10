import { ProductSkeleton } from '@/components/ProductDeals';

export default function Loading() {
  return (
    <main>
      <section className="hero loading-hero">
        <p className="eyebrow">Loading latest deals</p>
        <h1>Gadgets Mela</h1>
      </section>
      <section className="section product-section">
        <div className="section-heading">
          <p className="eyebrow">Please wait</p>
          <h2>Fetching WordPress product deals</h2>
        </div>
        <ProductSkeleton />
      </section>
    </main>
  );
}
