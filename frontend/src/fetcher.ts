class TokenContext {
  public readonly TOKEN_IDENTIFY = 'ifb_token';

  private token: string = null;

  public getToken(): string|null
  {
    if (this.token) {
      return this.token;
    }
    this.token = window.localStorage.getItem(this.TOKEN_IDENTIFY);
    if (!this.token) {
      return null;
    }
    return this.token;
  }

  public setToken(token: string): void
  {
    window.localStorage.setItem(this.TOKEN_IDENTIFY, token);
    this.token = token;
  }
}

const token = new TokenContext();

async function executeFetch<TReq, TRes>(method: string, path: string, request: TReq): Promise<TRes> {
  const body = JSON.stringify(request);
  const t = token.getToken();
  const headers = {
    'Content-Type': 'application/json; charset=utf-8',
    'Content-Length': body.length.toString(10),
    'Accept': 'application/json; charset=utf-8',
    ...t ? {'Authorization': `Bearer ${t}`}: {},
  };
  const response = await fetch(
    `${import.meta.env.VITE_API_URL}${path}`,
    {
      body,
      cache: 'no-cache',
      headers,
      keepalive: true,
      method,
    },
  );

  return response.json();
}

function isStub(): boolean {
  return import.meta.env.VITE_STUB === 'true';
}

export type HealthcheckRequest = {};

export type HealthcheckResponse = {};

export const HealthcheckResponseStub = {};

export async function healthcheck(): Promise<HealthcheckResponse> {
  if (isStub()) return Promise.resolve(HealthcheckResponseStub);
  return executeFetch<HealthcheckRequest, HealthcheckResponse>('GET', '/', {});
}

export type RegisterAuthRequest = {
  email: string,
};

export type RegisterAuthResponse = {
  password: string,
};

export const RegisterAuthResponseStub = {
  password: 'rawpassword',
};

export async function registerAuth(req: RegisterAuthRequest): Promise<RegisterAuthResponse> {
  if (isStub()) return Promise.resolve(RegisterAuthResponseStub);
  return executeFetch<RegisterAuthRequest, RegisterAuthResponse>('POST', '/api/auth/register', req);
}

export type LoginAuthRequest = {
  email: string,
  password: string,
};

export type LoginAuthResponse = {
  token: string,
};

export const LoginAuthResponseStub = {
  token: 'rawtoken',
};

export async function loginAuth(req: LoginAuthRequest): Promise<LoginAuthResponse> {
  if (isStub()) return Promise.resolve(LoginAuthResponseStub);
  const res = await executeFetch<LoginAuthRequest, LoginAuthResponse>('POST', '/api/auth/login', req);
  token.setToken(res.token);
  return res;
}

export type MeAuthRequest = {};

export type MeAuthResponse = {
  email: string,
};

export const MeAuthResponseStub = {
  email: 'test@ifb.test',
};

export async function meAuth(): Promise<MeAuthResponse> {
  if (isStub()) return Promise.resolve(MeAuthResponseStub);
  return executeFetch<MeAuthRequest, MeAuthResponse>('POST', '/api/auth/me', {});
}
