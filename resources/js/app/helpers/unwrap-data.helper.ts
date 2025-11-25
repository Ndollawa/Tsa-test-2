export const unwrapData = <T = any>(res: {
  status: boolean;
  code: number;
  message: string;
  payload: T;
}): T => {
  if (!res || typeof res !== "object" || !("payload" in res)) {
    throw new Error("Invalid response");
  }
  return res.payload;
};
