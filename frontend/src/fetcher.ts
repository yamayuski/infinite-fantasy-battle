export type HealthcheckRequest = {};

export type HealthcheckResponse = {};

let token: string = null;

async function executeFetch<TReq, TRes>(method: string, path: string, request: TReq): Promise<TRes> {
  const body = JSON.stringify(request);
  const headers = {
    'Content-Type': 'application/json; charset=utf-8',
    'Content-Length': body.length.toString(10),
    'Accept': 'application/json; charset=utf-8',
    ...token ? {'Authorization': `Bearer ${token}`}: {},
  };
  const response = await fetch(
    `${import.meta.env.VITE_API_URL}${path}`,
    {
      body,
      cache: 'no-cache',
      credentials: 'include',
      headers,
      keepalive: true,
      method,
      mode: 'cors',
    },
  );

  return response.json();
}

export async function healthcheck(): Promise<HealthcheckResponse> {
  return executeFetch<HealthcheckRequest, HealthcheckResponse>('GET', '/', {});
}

export type RegisterAuthRequest = {
  email: string,
};

export type RegisterAuthResponse = {
  password: string,
}

export async function registerAuth(req: RegisterAuthRequest): Promise<RegisterAuthResponse> {
  return executeFetch<RegisterAuthRequest, RegisterAuthResponse>('POST', '/api/auth/register', req);
}

export type LoginAuthRequest = {
  email: string,
  password: string,
};

export type LoginAuthResponse = {
  token: string,
};

export async function loginAuth(req: LoginAuthRequest): Promise<LoginAuthResponse> {
  const res = await executeFetch<LoginAuthRequest, LoginAuthResponse>('POST', '/api/auth/login', req);
  token = res.token;
  return res;
}

export type MeAuthRequest = {};

export type MeAuthResponse = {
  email: string,
};

export async function meAuth(): Promise<MeAuthResponse> {
  return executeFetch<MeAuthRequest, MeAuthResponse>('GET', '/api/auth/me', {});
}
