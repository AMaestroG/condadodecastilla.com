import { useEffect, useState, FormEvent } from 'react';
import './App.css';
import "./forum.css";

export interface Agent {
  name: string;
  bio: string;
  expertise: string;
  avatar: string;
  role_icon: string;
}

export interface Comment {
  comment: string;
  created_at: string;
}

interface ForumData {
  agents: Record<string, Agent>;
  comments: Record<string, Comment[]>;
  csrf?: string;
}

export default function Forum() {
  const [data, setData] = useState<ForumData | null>(null);
  const [newComment, setNewComment] = useState('');
  const [activeAgent, setActiveAgent] = useState<string>('');

  useEffect(() => {
    fetch('/foro/comments_api.php')
      .then(r => r.json())
      .then(d => {
        setData(d);
        const first = Object.keys(d.agents)[0];
        setActiveAgent(first);
      });
  }, []);

  if (!data) {
    return <p>Cargando...</p>;
  }

  const submit = async (e: FormEvent) => {
    e.preventDefault();
    if (!newComment.trim()) return;
    const res = await fetch('/foro/comments_api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        agent: activeAgent,
        comment: newComment,
        csrf_token: data.csrf,
      }),
    });
    if (res.ok) {
      const comment = await res.json();
      setData({
        ...data,
        comments: {
          ...data.comments,
          [activeAgent]: [comment, ...data.comments[activeAgent]],
        },
      });
      setNewComment('');
    }
  };

  return (
    <div className="forum">
      <div className="agents-menu">
        {Object.entries(data.agents).map(([id, ag]) => (
          <button
            key={id}
            className={id === activeAgent ? 'active' : ''}
            onClick={() => setActiveAgent(id)}
          >
            <img src={ag.avatar} alt="avatar" className="avatar-mini" />
            {ag.name}
          </button>
        ))}
      </div>
      <div className="agent-section">
        {(() => {
          const ag = data.agents[activeAgent];
          const comments = data.comments[activeAgent] || [];
          return (
            <div id={activeAgent}>
              <h2>
                <img src={ag.avatar} alt="avatar" className="agent-avatar" />
                {ag.name}
              </h2>
              <p>{ag.bio}</p>
              {ag.expertise && <p><strong>Especialidad:</strong> {ag.expertise}</p>}
              <form onSubmit={submit}>
                <textarea
                  value={newComment}
                  onChange={e => setNewComment(e.target.value)}
                  rows={3}
                  required
                  placeholder="Comparte tu consejo o comentario"
                />
                <button type="submit">Publicar</button>
              </form>
              <div className="comments-list">
                {comments.length === 0 ? (
                  <p style={{ fontStyle: 'italic' }}>Aún no hay comentarios.</p>
                ) : (
                  comments.map((c, i) => (
                    <div className="comment-item" key={i}>
                      <img src={ag.avatar} alt="avatar" className="comment-avatar" />
                      <div className="comment-content">
                        <p>{c.comment}</p>
                        <small>{c.created_at}</small>
                      </div>
                    </div>
                  ))
                )}
              </div>
            </div>
          );
        })()}
      </div>
    </div>
  );
}
