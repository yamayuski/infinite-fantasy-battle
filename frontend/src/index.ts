import { registerAuth } from "./fetcher";

window.addEventListener("load", async () => {
  const registerResult = await registerAuth({
    email: 'admin@ifb.test',
  });
  console.log(`password = ${registerResult.password}`);

}, { once: true });
