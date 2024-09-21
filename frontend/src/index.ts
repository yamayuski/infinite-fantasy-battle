import { loginAuth, meAuth, registerAuth } from "./fetcher";

const email = 'admin5@ifb.test';

window.addEventListener("load", async () => {
  const registerResult = await registerAuth({
    email,
  });
  console.log(`password = ${registerResult.password}`);

  const loginResult = await loginAuth({
    email,
    password: registerResult.password,
  });

  console.log(`token = ${loginResult.token}`);

  const meResult = await meAuth();

  console.log('me', meResult);

}, { once: true });
