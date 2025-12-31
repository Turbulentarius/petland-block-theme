import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import { resolve, dirname } from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = dirname(__filename)

// === Theme entries ===
const input = {
  petstyleColors: './src/colors.css',
  petstyle: './src/petstyle.css',
  petstyleEditor: './src/petstyle-editor.css',
  petWoocommerceMyaccount: './src/woocommerce/myaccount.css',
  petWoocommerceCheckoutFlow: './src/woocommerce/checkout-flow.css',
  petWoocommerceAdmin: './src/woocommerce/wc-tag-list-admin.css',
  categoryFilter: './src/scripts/category-filter.ts',
  tagsFilterAdmin: './src/scripts/tags-filter-admin.ts',
  priceFilter: './src/scripts/price-filter.ts',
  asideMenu: './src/scripts/aside-menu.ts',
  megaMenu: './src/scripts/mega-menu.ts',
  stickyHeader: './src/scripts/sticky-header.ts',
}

// === Export config ===
export default defineConfig({
  plugins: [tailwindcss()],
  build: {
    emptyOutDir: true,
    rollupOptions: {
      input,
      output: {
        dir: resolve(__dirname, 'dist'),
        format: 'es',
        entryFileNames: '[name].js',
        assetFileNames: '[name].[ext]',
      },
    },
  },
})
