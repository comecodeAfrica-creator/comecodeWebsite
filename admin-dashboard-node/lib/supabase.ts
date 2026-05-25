import { createClient } from '@supabase/supabase-js';

const supabaseUrl = (process.env.NEXT_PUBLIC_SUPABASE_URL ?? process.env.SUPABASE_URL ?? '') as string;
const anonKey = (process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY ?? '') as string;
const serviceRoleKey = (process.env.SUPABASE_SERVICE_ROLE_KEY ?? '') as string;

export function getPublicClient() {
  return createClient(supabaseUrl, anonKey, {
    auth: {
      persistSession: false,
      autoRefreshToken: false,
    },
  });
}

export function getServiceClient() {
  return createClient(supabaseUrl, serviceRoleKey, {
    auth: {
      persistSession: false,
      autoRefreshToken: false,
    },
  });
}
