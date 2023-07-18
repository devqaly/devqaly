import { defineConfig } from 'astro/config';
import starlight from '@astrojs/starlight';

// https://astro.build/config
export default defineConfig({
  integrations: [
    starlight({
      title: 'Devqaly Docs',
      social: {
        github: 'https://github.com/devqaly',
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
          ]
        },
        {
          label: 'Connect your backend',
          items: [
            { label: 'Connect Your Backend', link: '/getting-started/connect-backend' },
            { label: 'Backend - PHP', link: '/getting-started/backend/php' },
            { label: 'Backend - Laravel', link: '/getting-started/backend/laravel' },
          ]
        },
      ],
    }),
  ],

  // Process images with sharp: https://docs.astro.build/en/guides/assets/#using-sharp
  image: { service: { entrypoint: 'astro/assets/services/sharp' } },
});
