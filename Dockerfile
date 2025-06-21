# Frontend stage for Astro
FROM node:20-slim AS frontend
WORKDIR /app/frontend
RUN corepack enable
COPY package.json pnpm-lock.yaml* ./
RUN pnpm install
COPY . ./
EXPOSE 4321
CMD ["pnpm", "run", "dev", "--host", "0.0.0.0"]

# Backend stage for FastAPI
FROM python:3.11-slim AS backend
WORKDIR /app/backend
COPY backend/requirements.txt ./
RUN pip install --no-cache-dir -r requirements.txt
COPY backend ./
EXPOSE 8000
CMD ["uvicorn", "app.main:app", "--reload", "--host", "0.0.0.0", "--port", "8000"]

# Development stage
FROM python:3.11-slim AS development
WORKDIR /workspace
# Copy Python environment
COPY --from=backend /usr/local /usr/local
# Copy Node environment
COPY --from=frontend /usr/local /usr/local
# Copy app sources
COPY --from=frontend /app/frontend ./frontend
COPY --from=backend /app/backend ./backend
EXPOSE 4321 8000
CMD sh -c "cd frontend && pnpm run dev --host 0.0.0.0 & cd ../backend && uvicorn app.main:app --reload --host 0.0.0.0 --port 8000 && wait"
