import { defineConfig } from 'vite';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';

dotenv.config();

const __dirname = path.dirname(fileURLToPath(import.meta.url));

const themeName = 'boilerplate';
const themeDir = path.resolve(__dirname, 'themes', themeName);
const outDir = path.resolve(themeDir, 'assets');
const blocksDir = path.resolve(themeDir, 'template-parts/content/blocks');

// Auto-discover block-scoped JS: any blocks/<name>/<name>.js becomes its
// own Vite entry (keyed by folder name), so it builds separately and can
// be enqueued conditionally - see includes/asset-management.php.
function findBlockEntries() {
  const entries = {};
  if (!fs.existsSync(blocksDir)) return entries;
  for (const name of fs.readdirSync(blocksDir)) {
    const blockDir = path.join(blocksDir, name);
    if (!fs.statSync(blockDir).isDirectory()) continue;
    const jsFile = path.join(blockDir, `${name}.js`);
    if (fs.existsSync(jsFile)) {
      entries[name] = jsFile;
    }
  }
  return entries;
}

// writes/removes a marker file so PHP can detect "dev server is running"
function hotFilePlugin() {
  const hotFile = path.resolve(themeDir, 'hot');
  return {
    name: 'wp-hot-file',
    configureServer(server) {
      const address = `http://localhost:${server.config.server.port || 5173}`;
      fs.writeFileSync(hotFile, address);
      server.httpServer?.once('close', () => {
        if (fs.existsSync(hotFile)) fs.unlinkSync(hotFile);
      });
      process.on('exit', () => {
        if (fs.existsSync(hotFile)) fs.unlinkSync(hotFile);
      });
    },
  };
}

export default defineConfig(({ command }) => ({
  base: command === 'build' ? `/wp-content/themes/${themeName}/assets/` : '/',
  resolve: {
    alias: {
      // matches the scss loadPaths below - lets js reach into a
      // colocated block folder without a deep relative path.
      '@blocks': path.resolve(__dirname, `themes/${themeName}/template-parts/content/blocks`),
    },
  },
  css: {
    // without this, the dev server injects styles via JS with no sourcemap,
    // so devtools attributes rules to the injected <style> tag instead of
    // the originating .scss file/line.
    devSourcemap: true,
    preprocessorOptions: {
      scss: {
        // lets any partial - no matter where it lives, including
        // block folders colocated inside the theme - do
        // `@use "abstracts/mixins" as *;` without relative traversal.
        loadPaths: [path.resolve(__dirname, 'src/sass')],
      },
    },
  },
  plugins: [hotFilePlugin()],
  define: {
    global: 'globalThis',
  },
  server: {
    host: 'localhost',
    port: 5173,
    strictPort: true,
    cors: true,
    origin: 'http://localhost:5173',
  },
  build: {
    outDir,
    emptyOutDir: false,
    manifest: true,
    rollupOptions: {
        input: {
            app: path.resolve(__dirname, 'src/js/app.js'),
            ...findBlockEntries(),
        },
        output: {
            entryFileNames: 'js/[name].js',
            chunkFileNames: 'js/[name].js',
            assetFileNames: (assetInfo) => {
            if (/\.(woff2?|eot|ttf|otf)$/i.test(assetInfo.name)) return 'fonts/[name][extname]';
            if (/\.css$/i.test(assetInfo.name)) return 'css/[name][extname]';
            return 'assets/[name][extname]';
            },
        },
    },
  },
}));
