import { NextRequest, NextResponse } from 'next/server';
import { getServiceClient } from '@/lib/supabase';

export const dynamic = 'force-dynamic';

export async function GET() {
  const client = getServiceClient();
  const { data, error } = await client.from('contact_submissions').select('*').order('created_at', { ascending: false });

  if (error) return NextResponse.json({ error: error.message }, { status: 500 });
  return NextResponse.json({ items: data ?? [] });
}

export async function POST(request: NextRequest) {
  const payload = await request.json();
  const client = getServiceClient();

  const { error } = await client.from('contact_submissions').insert({
    name: payload.name,
    email: payload.email,
    company: payload.company ?? null,
    service: payload.service ?? null,
    message: payload.message,
    status: 'new',
  });

  if (error) return NextResponse.json({ error: error.message }, { status: 500 });
  return NextResponse.json({ ok: true });
}

export async function PATCH(request: NextRequest) {
  const payload = await request.json();
  const client = getServiceClient();

  const { error } = await client.from('contact_submissions').update({ status: payload.status }).eq('id', payload.id);
  if (error) return NextResponse.json({ error: error.message }, { status: 500 });
  return NextResponse.json({ ok: true });
}

export async function DELETE(request: NextRequest) {
  const { searchParams } = new URL(request.url);
  const id = Number(searchParams.get('id'));

  if (!id) return NextResponse.json({ error: 'Missing id.' }, { status: 400 });

  const client = getServiceClient();
  const { error } = await client.from('contact_submissions').delete().eq('id', id);

  if (error) return NextResponse.json({ error: error.message }, { status: 500 });
  return NextResponse.json({ ok: true });
}
