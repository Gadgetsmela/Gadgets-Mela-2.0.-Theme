import { ProductDeals } from '@/components/ProductDeals';
import { getWordPressProducts } from '@/lib/wordpress';
import type { ProductDeal } from '@/types/product';

const categories = ['Smartphones', 'Audio', 'Wearables', 'Laptops', 'Accessories'];
const blogPosts = ['How to choose the right gadget deal', 'Amazon affiliate buying checklist', 'Why WordPress powers Gadgets Mela deals'];

export default async function Home() {
  let products: ProductDeal[] = [];
  let hasError = false;

  try {
    products = await getWordPressProducts();
  } catch {
    hasError = true;
  }

  return (
    <main>
      <header className="site-header">
        <a className="logo" href="#top" aria-label="Gadgets Mela home">
          Gadgets Mela
        </a>
        <nav aria-label="Public storefront navigation">
          <a href="#categories">Categories</a>
          <a href="#deals">Product Deals</a>
          <a href="#blog">Blog</a>
          <a href="#whatsapp">WhatsApp CTA</a>
        </nav>
      </header>

      <section className="hero" id="top">
        <div>
          <p className="eyebrow">Premium public frontend</p>
          <h1>Fresh gadget deals managed from WordPress.</h1>
          <p>
            Gadgets Mela now uses WordPress as the admin CMS and this Next.js app as the fast public storefront for customers.
          </p>
          <a className="hero-cta" href="#deals">Shop latest deals</a>
        </div>
        <div className="hero-slider" aria-label="Hero slider highlights">
          <article>
            <span>01</span>
            <strong>Amazon affiliate ready</strong>
          </article>
          <article>
            <span>02</span>
            <strong>WhatsApp conversion buttons</strong>
          </article>
          <article>
            <span>03</span>
            <strong>Hot, featured, and trending filters</strong>
          </article>
        </div>
      </section>

      <section className="section" id="categories">
        <div className="section-heading">
          <p className="eyebrow">Browse by category</p>
          <h2>Categories</h2>
        </div>
        <div className="category-grid">
          {categories.map((category) => (
            <a href="#deals" key={category}>{category}</a>
          ))}
        </div>
      </section>

      {hasError ? (
        <section className="section error-state" id="deals">
          <p>Deals are updating. Please check again soon.</p>
        </section>
      ) : (
        <ProductDeals products={products} />
      )}

      <section className="section blog-section" id="blog">
        <div className="section-heading">
          <p className="eyebrow">Buying guides</p>
          <h2>Blog section</h2>
        </div>
        <div className="blog-grid">
          {blogPosts.map((post) => (
            <article key={post}>
              <p>Gadgets Mela Guide</p>
              <h3>{post}</h3>
              <span>Read more</span>
            </article>
          ))}
        </div>
      </section>

      <section className="section whatsapp-cta" id="whatsapp">
        <p className="eyebrow">Need a recommendation?</p>
        <h2>Get the best deal sent on WhatsApp.</h2>
        <a href="https://wa.me/919999999999?text=Send%20me%20the%20best%20Gadgets%20Mela%20deal" target="_blank" rel="noopener noreferrer">
          Chat with Gadgets Mela
        </a>
      </section>

      <footer className="site-footer">
        <p>© {new Date().getFullYear()} Gadgets Mela. WordPress-powered deals, Next.js-powered storefront.</p>
      </footer>
    </main>
  );
}
