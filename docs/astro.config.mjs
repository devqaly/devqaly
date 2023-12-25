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
          label: 'Getting Started',
          items: [
            { label: 'Introduction', link: '/getting-started/introduction' },
            { label: 'Quick Start', link: '/getting-started/quick-start' },
          ]
        },
        {
          label: 'Deployment',
          items: [
            { label: 'Introduction', link: '/deployment/introduction' },
            { label: 'Deploy from source', link: '/deployment/deploy-from-source' },
          ]
        },
        {
          label: 'Essentials',
          items: [
            { label: 'Events', items: [
                { label: 'Introduction', link: '/essentials/events/introduction' },
                { label: 'Network Requests', link: '/essentials/events/network-requests' },
                { label: 'Database Transactions', link: '/essentials/events/database-transactions' },
                { label: 'Clicks', link: '/essentials/events/clicks' },
                { label: 'Scrolls', link: '/essentials/events/scrolls' },
                { label: 'Resize Screen', link: '/essentials/events/resize-screen' },
                { label: 'URL Changes', link: '/essentials/events/url-changes' },
                { label: 'Logs', link: '/essentials/events/logs' },
                { label: 'Custom Events', link: '/essentials/events/custom-events' },
              ] }
          ]
        },
        {
          label: 'Connect your backend',
          items: [
            { label: 'Introduction', link: '/connect-your-backend/connect-backend' },
            { label: 'Backend - PHP', link: '/connect-your-backend/php' },
            { label: 'Backend - Laravel', link: '/connect-your-backend/laravel' },
          ]
        },
      ],
    }),
  ],

  // Process images with sharp: https://docs.astro.build/en/guides/assets/#using-sharp
  image: { service: { entrypoint: 'astro/assets/services/sharp' } },
});
