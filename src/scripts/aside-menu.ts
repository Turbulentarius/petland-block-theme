document.addEventListener('DOMContentLoaded', () => {
  const menuButton = document.querySelector<HTMLButtonElement>(
    '.petland-aside-menu-btn'
  )
  const menuCloseButton = document.querySelector<HTMLButtonElement>(
    '.petland-aside-menu-btn-close'
  )

  menuButton?.addEventListener('click', () => {
    document.body.classList.toggle('filter-menu-open')
    menuButton?.setAttribute('aria-expanded', 'true')
  })

  menuCloseButton?.addEventListener('click', () => {
    document.body.classList.toggle('filter-menu-open')
    menuButton?.setAttribute('aria-expanded', 'false')
  })

  // Remove class if the browser is resized to avoid problems with the desktop menus
  window.addEventListener('resize', () => {
    document.body.classList.remove('filter-menu-open')
    menuButton?.setAttribute('aria-expanded', 'false')
  })
})
