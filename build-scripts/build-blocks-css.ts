import { build } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import { readdirSync, statSync, mkdirSync } from 'fs'
import { join, resolve } from 'path'

const blocksDir = resolve('./blocks')
const blocks = readdirSync(blocksDir).filter(n =>
  statSync(join(blocksDir, n)).isDirectory()
)

for (const name of blocks) {
  const blockRoot = join(blocksDir, name)
  const cssFile = join(blockRoot, 'style.css')
  const targetDir = join(blockRoot, 'dist')

  mkdirSync(targetDir, { recursive: true })

  await build({
    plugins: [tailwindcss()],
    build: {
      emptyOutDir: false,
      rollupOptions: {
        input: cssFile,
        output: {
          dir: blockRoot + "/dist",
          assetFileNames: 'style.css',
        },
      },
    },
  })
}

