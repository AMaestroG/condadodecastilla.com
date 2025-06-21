from fastapi import FastAPI, Depends, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from sqlalchemy.orm import Session
import subprocess
import os

from .database import Base, engine, SessionLocal
from . import models, auth, schemas
from graph_db_interface import GraphDBInterface

Base.metadata.create_all(bind=engine)

app = FastAPI(title="Condado de Castilla API")
app.include_router(auth.router)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

db_interface = GraphDBInterface()

@app.post("/api/resource", status_code=201)
def add_resource(resource: dict):
    if not resource.get("url"):
        raise HTTPException(status_code=400, detail="'url' field is required")
    db_interface.add_or_update_resource(resource)
    return {"success": True}

@app.get("/api/resource")
def list_resources():
    return db_interface.get_all_resources()

@app.post("/api/chat")
def chat(data: dict):
    prompt = str(data.get("prompt", "")).strip()
    if not prompt:
        raise HTTPException(status_code=400, detail="'prompt' field is required")
    script_path = os.path.join(os.path.dirname(__file__), "..", "..", "scripts", "chat_cli.php")
    try:
        result = subprocess.run([
            "php", script_path, prompt
        ], capture_output=True, text=True, check=False)
        if result.returncode != 0:
            error_msg = result.stderr.strip() or "Unknown error"
            raise HTTPException(status_code=500, detail=f"PHP error: {error_msg}")
        return {"response": result.stdout.strip()}
    except Exception as exc:
        raise HTTPException(status_code=500, detail=str(exc))
