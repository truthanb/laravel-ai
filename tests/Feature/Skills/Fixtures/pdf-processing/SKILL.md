---
name: pdf-processing
description: Extract text and tables from PDF files, fill forms, merge documents. Use when working with PDF documents or when the user mentions PDFs, forms, or document extraction.
license: MIT
metadata:
  author: test-author
  version: "1.0"
---

# PDF Processing Skill

## When to use this skill

Use this skill whenever the user needs to:
- Extract text or tables from PDF files
- Fill out PDF forms programmatically
- Merge multiple PDF files into one
- Split PDF files

## Available operations

### Extract text from PDF

To extract text from a PDF file:

1. Use the `extract_text` function with the PDF file path
2. The function returns plain text content
3. Tables are preserved as tab-separated values

### Fill PDF forms

To fill a PDF form:

1. Identify the form fields using `list_fields`
2. Create a mapping of field names to values
3. Use `fill_form` with the mapping

### Merge PDFs

To merge multiple PDFs:

1. Provide an array of PDF file paths in the desired order
2. Use `merge_pdfs` function
3. Specify the output file path

## Example usage

```python
# Extract text
text = extract_text("document.pdf")

# Fill form
fields = {"name": "John Doe", "email": "john@example.com"}
fill_form("form.pdf", fields, "filled_form.pdf")

# Merge PDFs
merge_pdfs(["doc1.pdf", "doc2.pdf", "doc3.pdf"], "merged.pdf")
```
