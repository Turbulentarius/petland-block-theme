import { build } from 'vite'
import { readdirSync, statSync, mkdirSync } from 'fs'
import { join, resolve } from 'path'

const blocksDir = resolve('./blocks')
const blocks = readdirSync(blocksDir).filter(n =>
  statSync(join(blocksDir, n)).isDirectory()
)

for (const name of blocks) {
  const blockRoot = join(blocksDir, name)
  const jsFile = join(blockRoot, 'index.jsx')
  const outDir = join(blockRoot, 'dist')

  mkdirSync(outDir, { recursive: true })

  await build({
    build: {
      emptyOutDir: false,
      rollupOptions: {
        input: jsFile,
        external: [
          '@wordpress/blocks',
          '@wordpress/i18n',
          '@wordpress/block-editor',
          '@wordpress/components',
          '@wordpress/element',
          '@wordpress/api-fetch',
        ],
        output: {
          dir: outDir,
          format: 'iife',
          entryFileNames: 'script.js',
          globals: {
            '@wordpress/blocks': 'wp.blocks',
            '@wordpress/i18n': 'wp.i18n',
            '@wordpress/block-editor': 'wp.blockEditor',
            '@wordpress/components': 'wp.components',
            '@wordpress/element': 'wp.element',
            '@wordpress/api-fetch': 'wp.apiFetch',
          },
        },
      },
    },
  })
}
