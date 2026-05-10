export type ProductDeal = {
  id: number;
  title: string;
  brand: string;
  category: string;
  description: string;
  image: string;
  amazonUrl: string;
  regularPrice: number;
  salePrice: number;
  discount: number;
  rating: number;
  reviews: number;
  isHotDeal: boolean;
  isFeatured: boolean;
  trendingScore: number;
  whatsappMessage: string;
  updatedAt: string;
};
