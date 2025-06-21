# Frontend stage for Astro
FROM node:20-slim AS frontend
WORKDIR /app/frontend
RUN corepack enable
COPY package.json pnpm-lock.yaml* ./
RUN pnpm install
COPY . ./
EXPOSE 4321
CMD ["pnpm", "run", "dev", "--host", "0.0.0.0"]

# Backend stage for Flask
FROM python:3.11-slim AS backend
WORKDIR /app
COPY requirements.txt ./
RUN pip install --no-cache-dir -r requirements.txt
COPY flask_app.py ./
EXPOSE 5000
CMD ["python", "flask_app.py"]

# Development stage
FROM python:3.11-slim AS development
WORKDIR /workspace
# Copy Python environment
COPY --from=backend /usr/local /usr/local
# Copy Node environment
COPY --from=frontend /usr/local /usr/local
# Copy app sources
COPY --from=frontend /app/frontend ./frontend
COPY --from=backend /app/flask_app.py ./flask_app.py
EXPOSE 4321 5000
CMD sh -c "cd frontend && pnpm run dev --host 0.0.0.0 & cd .. && python flask_app.py & wait"
