async function loadProducts (category?: string): Promise<void> {
  let url = '/wp-json/custom/v1/products'
  if (category) url += '/' + encodeURIComponent(category)

  // append current page query string (if any) to REST URL
  const searchParams = window.location.search
  if (searchParams) {
    const separator = url.includes('?') ? '&' : '?'
    url += separator + searchParams.substring(1) // remove leading "?"
  }

  try {
    // const noProductsFound = document.getElementById('woocommerce-no-products-found')
    const topDescription = document.querySelector('.petland-top-description')
    const products = document.querySelector('.petland-woo-products') as HTMLElement
    const pagination = document.querySelector('.woocommerce-pagination')
    const breadcrumb = document.querySelector('.woocommerce-breadcrumb')
    const pageTitle = document.querySelector('.page-title')
    const description = document.querySelector('.woocommerce-products-header')

    const resultsCount = document.querySelector('.woocommerce-result-count')
    const ordering = document.querySelector('.woocommerce-ordering')

    if (products) {
      const spinnerElm = `<div role="status">
    <svg aria-hidden="true" class="w-full h-full text-main-background animate-spin fill-main" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
    </svg></div>`
      const currentHeight = products.getBoundingClientRect().height
      products.style.height = `${currentHeight}px`
      products.innerHTML = `
      <li class="product product-loading">` + spinnerElm + `</li>
      <li class="product product-loading">` + spinnerElm + `</li>
      <li class="product product-loading">` + spinnerElm + `</li>
      <li class="product product-loading">` + spinnerElm + `</li>`
    }

    const res = await fetch(url)
    if (!res.ok) throw new Error(`HTTP error: ${res.status}`)

    const data = await res.json()

    // If no products are returned, hide various UI elements that are not used
    if (data.no_products_found) {
      const noProductsContainer = document.createElement('div')
      noProductsContainer.innerHTML = data.no_products_found
      pagination?.remove()
      resultsCount?.remove()
      ordering?.remove()
      if (products) products.replaceWith(noProductsContainer)
    }

    if (products) {
      products.innerHTML = data.products_html
      products.style.height = 'auto'
    }
    
    if (pagination && data.pagination_html) {
        pagination.innerHTML = data.pagination_html
    }

    if (breadcrumb && data.breadcrumb_html) {
        breadcrumb.innerHTML = data.breadcrumb_html
    }
    if (pageTitle && data.title_html) {
        pageTitle.innerHTML = data.title_html
    }
    if (description && data.description_html) {
        description.innerHTML = data.description_html
    }
    if (resultsCount && data.result_count_html) {
        resultsCount.outerHTML = data.result_count_html
    }
    if (topDescription && data.top_description_html) {
        topDescription.innerHTML = data.top_description_html
    } else if (topDescription) {
      topDescription.innerHTML = '';
    }

    const subcategoriesList = document.querySelector(
      '.petland-subcategory-list'
    )
    if (subcategoriesList) {
      // Remove old event listeners by cloning the node
      const newContainer = subcategoriesList.cloneNode(false) as HTMLElement
      subcategoriesList.replaceWith(newContainer)
      newContainer.innerHTML = data.categories_html
      attachPetlandCategoryHandlers(newContainer)
    }
  } catch (err) {
    console.error('Error fetching products:', err)
  }
}

// Attach click events to all category links
function attachPetlandCategoryHandlers (
  container: HTMLElement | Document = document
) {
  if (!document.querySelector('.petland-woo-products')) return
  container
    .querySelectorAll<HTMLAnchorElement>('.petland-category-link')
    .forEach(link => {
      link.addEventListener('click', event => {
        event.preventDefault()
        const catSlug = link.dataset.catslug
        if (!catSlug) return
        loadProducts(catSlug).then(() => setCategoryUrl(link))
      })
    })
}
function setCategoryUrl (link: HTMLAnchorElement) {
  const href = link.href
  window.history.pushState({ category: link.dataset.catslug }, '', href)
}



window.addEventListener('popstate', event => {
  const catSlug = event.state?.category as string | undefined
  if (catSlug) {
    loadProducts(catSlug)
  }
})

window.addEventListener('DOMContentLoaded', () => {
  attachPetlandCategoryHandlers()
});