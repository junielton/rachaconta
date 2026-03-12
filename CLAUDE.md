# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Tchurminha** is a bill-splitting web app (racha conta) built as a single-file PHP application (`index.php`). It allows a group of people to split shared expenses by assigning items to individuals and calculating each person's share.

## Architecture

The entire app lives in `index.php` — a single file containing:

1. **PHP backend** (lines 1–48): A simple JSON API with two actions:
   - `?action=load` — reads state from `data.json` (creates default if missing)
   - `?action=save` — writes posted JSON state to `data.json`

2. **HTML/CSS** (lines 50–338): Dark-themed UI with DM Sans + JetBrains Mono fonts, custom checkboxes, responsive layout.

3. **Vanilla JS** (lines 390–636): Client-side state management with:
   - State shape: `{ people: string[], items: { name, price, checks: boolean[] }[] }`
   - Debounced auto-save (600ms) to both `localStorage` and server
   - `localStorage` fallback when server is unreachable
   - Paste from Excel (tab or semicolon-separated), JSON import/export

## Running Locally

```bash
php -S localhost:8000
```

No build step, no dependencies. Just serve with PHP's built-in server.

## Data Persistence

- Server: `data.json` in project root (gitignored or generated at runtime)
- Client: `localStorage` key `tchurminha`

## Language

The UI is in Brazilian Portuguese (pt-BR). Variable names and code are in English.
