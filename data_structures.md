# Data Structures

This document outlines the proposed data structures for `WebResource` and `Link` entities.

## WebResource

Represents a web resource, such as a webpage or a PDF document.

```python
class WebResource:
    id: str  # Unique identifier for the web resource (e.g., UUID)
    url: str  # URL of the web resource
    content: str  # Text content of the web resource
    last_crawled_at: datetime  # Timestamp of the last crawl
    metadata: dict  # Additional metadata (e.g., language, content type)
```

## Link

Represents a hyperlink between two web resources.

```python
class Link:
    id: str  # Unique identifier for the link (e.g., UUID)
    source_resource_id: str  # ID of the source WebResource
    target_resource_id: str  # ID of the target WebResource
    anchor_text: str  # Anchor text of the link
    created_at: datetime  # Timestamp of when the link was discovered
```
