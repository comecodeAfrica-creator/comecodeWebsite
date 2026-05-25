import { NextRequest, NextResponse } from 'next/server';
import { getServiceClient } from '@/lib/supabase';

export const dynamic = 'force-dynamic';

export async function GET(request: NextRequest) {
  const { searchParams } = new URL(request.url);
  const scope = searchParams.get('scope');
  const client = getServiceClient();

  if (scope === 'gallery') {
    const { data, error } = await client.from('gallery').select('*').order('created_at', { ascending: false });
    if (error) return NextResponse.json({ error: error.message }, { status: 500 });
    return NextResponse.json({ items: data ?? [] });
  }

  if (scope === 'events') {
    const { data, error } = await client.from('events').select('*').order('created_at', { ascending: false });
    if (error) return NextResponse.json({ error: error.message }, { status: 500 });
    return NextResponse.json({ items: data ?? [] });
  }

  return NextResponse.json({ error: 'Unsupported scope.' }, { status: 400 });
}

export async function POST(request: NextRequest) {
  const { searchParams } = new URL(request.url);
  const scope = searchParams.get('scope');
  const payload = await request.json();

  const client = getServiceClient();

  if (scope === 'gallery') {
    const { error } = await client.from('gallery').insert({
      caption: payload.caption ?? '',
      image_url: payload.image_url,
    });

    if (error) return NextResponse.json({ error: error.message }, { status: 500 });
    return NextResponse.json({ ok: true });
  }

  if (scope === 'events') {
    const { error } = await client.from('events').insert({
      title: payload.title,
      date: payload.date,
      description: payload.description ?? '',
      image_url: payload.image_url,
    });

    if (error) return NextResponse.json({ error: error.message }, { status: 500 });
    return NextResponse.json({ ok: true });
  }

  return NextResponse.json({ error: 'Unsupported scope.' }, { status: 400 });
}

export async function DELETE(request: NextRequest) {
  const { searchParams } = new URL(request.url);
  const scope = searchParams.get('scope');
  const id = Number(searchParams.get('id'));

  if (!id) {
    return NextResponse.json({ error: 'Missing id.' }, { status: 400 });
  }

  const client = getServiceClient();

  if (scope === 'gallery') {
    const { error } = await client.from('gallery').delete().eq('id', id);
    if (error) return NextResponse.json({ error: error.message }, { status: 500 });
    return NextResponse.json({ ok: true });
  }

  if (scope === 'events') {
    const { error } = await client.from('events').delete().eq('id', id);
    if (error) return NextResponse.json({ error: error.message }, { status: 500 });
    return NextResponse.json({ ok: true });
  }

  return NextResponse.json({ error: 'Unsupported scope.' }, { status: 400 });
}
