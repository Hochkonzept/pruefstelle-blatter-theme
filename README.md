# KÜS Prüfstelle Blatter – WordPress-Theme

Custom-Theme für pruefstelle-blatter.de. Es ersetzt Neve, Elementor und Elementor Pro und braucht keinen Page-Builder.

## Aufbau

| Bereich | Wo gepflegt |
|---|---|
| Seiteninhalte (Hero, Texte, FAQ, CTA, Schritte) | Block-Editor der jeweiligen Seite |
| Standorte (Adresse, Telefon, Öffnungszeiten, **Hinweis**) | Backend → Prüfstelle → Standorte |
| Startseiten-Slider (roter Kasten + Bildkacheln) | Backend → Prüfstelle → Startseiten-Slider |
| Bewertungen | Backend → Prüfstelle → Bewertungen |
| Preise (2 Sets), Google-Bewertung, News-Zeile, Grundsätze, Footer | Backend → Prüfstelle → Einstellungen |

Wiederkehrende Sektionen stehen als Shortcode in den Seiten:
`[blatter_slider]`, `[blatter_termin]`, `[blatter_preise set="standard|steinen"]`, `[blatter_bewertungen]`,
`[blatter_standorte orte="freiburg,bad-saeckingen,steinen"]`, `[blatter_beitraege]`, `[blatter_jobs]`, `[blatter_ortswappen]`.

Standorte und Slides sind per REST-API schreibbar (`/wp-json/wp/v2/blatter_standort/<id>` mit `meta.hinweis`, `meta.zeit_freitag` usw.). Damit lassen sich Öffnungszeiten später per n8n aus WhatsApp oder Telegram ändern.

## Benötigte Plugins

- Simply Schedule Appointments (Buchungskalender, `[ssa_booking]`)
- Yoast SEO (Titel und Meta-Descriptions bleiben unverändert)

Nach dem Umstieg können Elementor, Elementor Pro, Neve, Orbit Fox (obfx), Trustindex und Autoptimize deaktiviert werden.

## Umstieg auf dem Staging

1. Live-Seite in Mittwald als Staging-App klonen (Datenbank + Uploads).
2. Theme nach `wp-content/themes/pruefstelle-blatter` deployen (GitHub Action, siehe unten).
3. Import ausführen:
   ```bash
   wp eval-file wp-content/themes/pruefstelle-blatter/import/import.php
   wp theme activate pruefstelle-blatter
   ```
   Der Import ist mehrfach ausführbar. Alte Seiteninhalte werden in `_blatter_backup_content` gesichert, Elementor-Daten bleiben erhalten.
4. Seiten durchklicken, danach Elementor & Co. deaktivieren.

## Deployment

Bei einem Push auf `main` deployt die GitHub Action auf das Staging. Live geht es nur manuell über „Run workflow“ mit `target=live`.

Secrets je Environment (`staging`, `live`):
`MITTWALD_SSH_HOST`, `MITTWALD_SSH_USER`, `MITTWALD_SSH_KEY`, `MITTWALD_THEME_PATH`
(z. B. `/html/<app>/wp-content/themes/pruefstelle-blatter`).

## Neu erzeugen

`import/` wurde mit einem Konverter direkt aus dem HTML der Live-Seite erzeugt (Stand 14.11.2026). Das Skript liegt nicht im Theme.
