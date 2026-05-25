# ComeCode Admin Dashboard (Node.js + Supabase)

This is a standalone Vercel-ready admin dashboard for the ComeCode website.

## Environment variables

Create a `.env.local` file with:

```env
NEXT_PUBLIC_SUPABASE_URL=your-supabase-project-url
NEXT_PUBLIC_SUPABASE_ANON_KEY=your-anon-public-key
SUPABASE_SERVICE_ROLE_KEY=your-service-role-key
```

## Supabase schema

Run the SQL in `supabase-schema.sql` in your Supabase SQL editor.

## Run locally

```bash
npm install
npm run dev
```

## Build for production

```bash
npm run build
```
