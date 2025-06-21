import { render, screen } from '@testing-library/react';
import Forum from './Forum';
import { vi } from 'vitest';

describe('<Forum />', () => {
  beforeEach(() => {
    vi.spyOn(global, 'fetch').mockReturnValue(new Promise(() => {}) as any);
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it('renders loading state initially', () => {
    render(<Forum />);
    expect(screen.getByText(/Cargando/i)).toBeInTheDocument();
  });
});
