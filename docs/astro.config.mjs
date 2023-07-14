import { defineConfig } from 'astro/config';
import starlight from '@astrojs/starlight';

// https://astro.build/config
export default defineConfig({
  integrations: [
    starlight({
      title: 'My Docs',
      social: {
        github: 'https://github.com/withastro/starlight',
      },
      sidebar: [
        {
          label: 'Overview',
          autogenerate: { directory: 'overview' },
        },
        {
          label: 'Getting Started',
          items: [
            { label: 'Quick Start', link: '/getting-started/quickstart' },
            { label: 'Connect Your Backend', link: '/getting-started/connect-backend' },
            { label: 'Backend - PHP', link: '/getting-started/backend/php' },
          ]
        },
      ],
    }),
  ],

  // Process images with sharp: https://docs.astro.build/en/guides/assets/#using-sharp
  image: { service: { entrypoint: 'astro/assets/services/sharp' } },
});
