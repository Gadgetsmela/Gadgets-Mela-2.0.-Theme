import type { ProductDeal } from '@/types/product';

export const WORDPRESS_PRODUCTS_ENDPOINT =
  'https://gadgetsmela2.com/wp-json/gadgets-mela/v1/products';

export async function getWordPressProducts(): Promise<ProductDeal[]> {
  const response = await fetch(WORDPRESS_PRODUCTS_ENDPOINT, {
    next: { revalidate: 300 },
    headers: {
      Accept: 'application/json'
    }
  });

  if (!response.ok) {
    throw new Error(`WordPress products API failed with status ${response.status}`);
  }

  const products = (await response.json()) as ProductDeal[];
  return products.filter((product) => product.title && product.amazonUrl);
}
