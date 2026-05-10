'use client';

import { useMemo, useState } from 'react';
import type { ProductDeal } from '@/types/product';

type ProductDealsProps = {
  products: ProductDeal[];
};

const whatsappNumber = process.env.NEXT_PUBLIC_WHATSAPP_NUMBER ?? '919999999999';

const formatPrice = (price: number) =>
  new Intl.NumberFormat('en-IN', {
    style: 'currency',
    currency: 'INR',
    maximumFractionDigits: 0
  }).format(price);

function buildWhatsAppUrl(message: string) {
  return `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
}

export function ProductDeals({ products }: ProductDealsProps) {
  const [category, setCategory] = useState('all');
  const [hotOnly, setHotOnly] = useState(false);
  const [featuredOnly, setFeaturedOnly] = useState(false);
  const [trendingOnly, setTrendingOnly] = useState(false);

  const categories = useMemo(
    () => ['all', ...Array.from(new Set(products.map((product) => product.category).filter(Boolean)))],
    [products]
  );

  const filteredProducts = products.filter((product) => {
    if (category !== 'all' && product.category !== category) {
      return false;
    }
    if (hotOnly && !product.isHotDeal) {
      return false;
    }
    if (featuredOnly && !product.isFeatured) {
      return false;
    }
    if (trendingOnly && product.trendingScore <= 0) {
      return false;
    }
    return true;
  });

  return (
    <section className="section product-section" id="deals">
      <div className="section-heading">
        <p className="eyebrow">Live WordPress deals</p>
        <h2>Product deals</h2>
        <p>Every card below is pulled from the WordPress Product Deals CMS.</p>
      </div>

      <div className="filters" aria-label="Product deal filters">
        <label>
          Category
          <select value={category} onChange={(event) => setCategory(event.target.value)}>
            {categories.map((categoryName) => (
              <option key={categoryName} value={categoryName}>
                {categoryName === 'all' ? 'All categories' : categoryName}
              </option>
            ))}
          </select>
        </label>
        <button className={hotOnly ? 'active' : ''} type="button" onClick={() => setHotOnly((value) => !value)}>
          Hot Deals
        </button>
        <button className={featuredOnly ? 'active' : ''} type="button" onClick={() => setFeaturedOnly((value) => !value)}>
          Featured
        </button>
        <button className={trendingOnly ? 'active' : ''} type="button" onClick={() => setTrendingOnly((value) => !value)}>
          Trending
        </button>
      </div>

      {filteredProducts.length === 0 ? (
        <p className="empty-state">No matching WordPress deals found for this filter.</p>
      ) : (
        <div className="product-grid">
          {filteredProducts.map((product) => (
            <article className="product-card" key={product.id}>
              <div className="product-image" style={{ backgroundImage: `url(${product.image})` }}>
                {product.isHotDeal ? <span>Hot Deal</span> : null}
              </div>
              <div className="product-content">
                <div className="product-meta">
                  <span>{product.brand}</span>
                  <span>{product.category}</span>
                </div>
                <h3>{product.title}</h3>
                <p>{product.description}</p>
                <div className="rating-row">
                  <strong>★ {product.rating.toFixed(1)}</strong>
                  <span>{product.reviews.toLocaleString('en-IN')} reviews</span>
                  {product.discount > 0 ? <em>{product.discount}% OFF</em> : null}
                </div>
                <div className="price-row">
                  <strong>{formatPrice(product.salePrice)}</strong>
                  {product.regularPrice > product.salePrice ? <span>{formatPrice(product.regularPrice)}</span> : null}
                </div>
                <div className="card-actions" aria-label={`${product.title} actions`}>
                  <a className="primary" href={product.amazonUrl} rel="nofollow sponsored noopener noreferrer" target="_blank">
                    BUY NOW
                  </a>
                  <a href={product.amazonUrl} rel="nofollow sponsored noopener noreferrer" target="_blank">
                    QUICK VIEW
                  </a>
                  <a href={buildWhatsAppUrl(product.whatsappMessage || `I want to buy ${product.title}`)} target="_blank" rel="noopener noreferrer">
                    Buy via WhatsApp
                  </a>
                  <a href={buildWhatsAppUrl(`Sharing this Gadgets Mela deal: ${product.title} - ${product.amazonUrl}`)} target="_blank" rel="noopener noreferrer">
                    Send Deal to WhatsApp
                  </a>
                </div>
              </div>
            </article>
          ))}
        </div>
      )}
    </section>
  );
}

export function ProductSkeleton() {
  return (
    <div className="product-grid skeleton-grid" aria-label="Loading product deals">
      {[0, 1, 2, 3].map((item) => (
        <div className="product-card skeleton" key={item}>
          <div className="product-image" />
          <div className="product-content">
            <span />
            <h3 />
            <p />
            <p />
            <div />
          </div>
        </div>
      ))}
    </div>
  );
}
