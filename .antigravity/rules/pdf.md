---
description: Standards for PDF generation and Arabic typography configuration
globs:
  - app/Services/Pdf*.php
  - app/Http/Controllers/*Pdf*.php
  - resources/views/pdf/**/*.blade.php
---

# PDF & Arabic Typography Standards

- **Arabic Font Rendering:** Use Amiri or Cairo fonts for Arabic text output. Specify the font-family explicitly in the styling.
- **RTL Layout configuration:** Ensure the PDF generator (mpdf/dompdf) is configured to handle RTL text rendering and direction.
- **Encoding:** Verify all text passed to the PDF engine is UTF-8 encoded.
- **Page Layouts:** Explicitly control page breaks and prevent overlapping elements by utilizing proper CSS rules (`page-break-inside: avoid;`).
- **Security:** Do not render unvalidated/unescaped user input inside PDF templates.
