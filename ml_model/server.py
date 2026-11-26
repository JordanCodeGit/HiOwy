"""
Optional Python HTTP inference server for HiOwy Bullying NLP model (model_terpilih.joblib).
Note: By default HiOwy runs the exact same extracted Linear SVM weights natively in PHP
(ML_DRIVER=local) so no separate Python daemon is required. Set ML_DRIVER=http and
ML_SERVICE_URL=http://127.0.0.1:8001 in .env if you want to serve via Python instead.
"""

from pathlib import Path
import re
import joblib
from fastapi import FastAPI
from pydantic import BaseModel
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
from Sastrawi.StopWordRemover.StopWordRemoverFactory import StopWordRemoverFactory

MODEL_PATH = Path(__file__).parent / "model_terpilih.joblib"

SLANG_DICT = {
    "akuu": "aku", "aq": "aku", "gw": "aku", "gue": "aku", "nyong": "aku", "inyong": "aku",
    "gak": "tidak", "ga": "tidak", "gk": "tidak", "nggak": "tidak", "ngga": "tidak", "nggk": "tidak",
    "enggak": "tidak", "engga": "tidak", "nda": "tidak", "tdk": "tidak", "ora": "tidak", "ra": "tidak",
    "gaada": "tidak ada", "blm": "belum", "jngn": "jangan", "jgn": "jangan", "aja": "saja", "aj": "saja",
    "bae": "saja", "tok": "saja", "yg": "yang", "yng": "yang", "dgn": "dengan", "krn": "karena",
    "karna": "karena", "krna": "karena", "trs": "terus", "trus": "terus", "trss": "terus", "tp": "tapi",
    "tpi": "tapi", "jd": "jadi", "jdi": "jadi", "lg": "lagi", "bgt": "banget", "bngt": "banget",
    "bangey": "banget", "bnget": "banget", "skrg": "sekarang", "skrng": "sekarang", "skrang": "sekarang",
    "sekrang": "sekarang", "udh": "sudah", "udah": "sudah", "udahh": "sudah", "dah": "sudah", "sdh": "sudah",
    "uda": "sudah", "wis": "sudah", "uwis": "sudah", "blg": "bilang", "bilng": "bilang", "bilangin": "bilang",
    "ngmng": "ngomong", "ngomg": "ngomong", "ngmg": "ngomong", "ngoong": "ngomong", "ngomng": "ngomong",
    "ngomohg": "ngomong", "omong": "ngomong", "td": "tadi", "tdi": "tadi", "tadii": "tadi", "bsk": "besok",
    "bsok": "besok", "besuk": "besok", "kmrn": "kemarin", "kemaren": "kemarin", "kemren": "kemarin",
    "kmarin": "kemarin", "kemareen": "kemarin", "kearen": "kemarin", "kemarren": "kemarin", "kejaren": "kemarin",
    "hr": "hari", "pdhl": "padahal", "msh": "masih", "masi": "masih", "msih": "masih", "isih": "masih",
    "knp": "kenapa", "kenpa": "kenapa", "org": "orang", "wong": "orang", "gmn": "bagaimana", "gimana": "bagaimana",
    "kepriwe": "bagaimana", "priwe": "bagaimana", "kl": "kalau", "kalo": "kalau", "klo": "kalau", "kalok": "kalau",
    "sm": "sama", "sma": "sama", "dr": "dari", "utk": "untuk", "bkn": "bukan", "emg": "memang", "bkin": "bikin",
    "bikn": "bikin", "nyari": "cari", "nyarii": "cari", "dikit": "sedikit", "gitu": "begitu", "gini": "begini",
    "plg": "pulang", "pulng": "pulang", "ortu": "orang tua", "mamah": "ibu", "mama": "ibu", "bapak": "ayah",
    "kls": "kelas", "kela": "kelas", "kelass": "kelas", "kels": "kelas", "tmn": "teman", "tmen": "teman",
    "temen": "teman", "temenn": "teman", "temennya": "temannya", "temenya": "temannya", "tmnnya": "temannya",
    "sekolh": "sekolah", "skolah": "sekolah", "sekllah": "sekolah", "sekopah": "sekolah", "skolh": "sekolah",
    "sekolsh": "sekolah", "plajaran": "pelajaran", "pelaajran": "pelajaran", "peljaran": "pelajaran",
    "pelqjaran": "pelajaran", "pelajraan": "pelajaran", "pelaajaran": "pelajaran", "istrahat": "istirahat",
    "istirht": "istirahat", "istrhat": "istirahat", "istiraht": "istirahat", "istirwhat": "istirahat",
    "kakel": "kakak kelas", "bk": "bk", "uks": "uks", "pr": "pr", "osis": "osis", "takutt": "takut",
    "tkt": "takut", "taukt": "takut", "takuut": "takut", "tajut": "takut", "takkut": "takut", "crita": "cerita",
    "ceritaa": "cerita", "nangis": "menangis", "gemeter": "gemetar", "kepikiran": "kepikiran",
    "kpikiran": "kepikiran", "kepikirn": "kepikiran", "kepikiarn": "kepikiran", "kepikoran": "kepikiran",
    "kepikiraan": "kepikiran", "keikiran": "kepikiran", "kepikirna": "kepikiran", "kepikifan": "kepikiran",
    "ketenu": "ketemu", "keteum": "ketemu", "kettemu": "ketemu", "keetmu": "ketemu", "ketmeu": "ketemu",
    "keteemu": "ketemu", "keyemu": "ketemu", "merem": "tidur", "melek": "terjaga", "mumet": "pusing",
    "lara": "sakit", "cape": "capek", "capekk": "capek", "baper": "bawa perasaan", "baperan": "bawa perasaan",
    "caper": "cari perhatian", "ilfeel": "ilfil", "ning": "di", "neng": "di", "endi": "mana", "salahe": "salahnya",
    "kudu": "harus", "meneng": "diam", "kaya": "seperti", "kayak": "seperti", "kuwe": "itu", "iki": "ini",
    "kae": "itu", "sing": "yang", "rasane": "rasanya", "penak": "enak", "tenan": "sungguh", "wingi": "kemarin",
    "dina": "hari", "maning": "lagi", "dheweke": "dia", "ndelok": "lihat", "bali": "pulang", "mung": "hanya",
    "sinau": "belajar", "digagas": "dipedulikan", "digugu": "didengar", "omongna": "bicarakan",
    "diterusna": "diteruskan", "diteruske": "diteruskan", "rampung": "selesai", "jebule": "ternyata",
    "mandeg": "berhenti", "cah": "anak", "nanggung": "menanggung", "jarene": "katanya", "guyon": "bercanda",
    "wani": "berani", "pekok": "bodoh", "liwat": "lewat", "mben": "dulu", "ben": "biar", "carane": "caranya",
    "meh": "hampir", "ngono": "begitu", "ngene": "begini", "lah": "", "yo": "ya", "diulangi": "diulang",
    "asw": "anjing", "anj": "anjing", "anjr": "anjing", "anjir": "anjing", "njir": "anjing", "bgst": "bangsat",
    "jancuk": "jancuk", "ndasmu": "kepalamu", "cangkemmu": "mulutmu", "kampret": "kampret",
    "gc": "grup", "notif": "notifikasi", "mabar": "main bersama", "hp": "ponsel", "dm": "pesan", "chatnya": "chat",
}

NEGASI = {"tidak", "belum", "jangan", "bukan", "tanpa", "kurang"}
STOPWORDS = set(StopWordRemoverFactory().get_stop_words()) | {
    "aku", "kamu", "dia", "mereka", "kita", "kami", "nya", "sih", "deh", "dong", "kok", "loh", "lho", "woy", "btw",
    "duh", "nih", "tuh", "ya", "yah", "kan", "pun", "si", "kak", "eh", "oh", "ah", "wkwk", "hehe", "gitu", "begitu",
    "begini", "terus", "tapi", "jadi", "lagi", "saja", "sudah", "yang", "dan", "di", "ke", "dari", "ini", "itu", "ada",
    "sama", "mau", "bisa", "pas", "waktu", "kalau", "banget", "sekarang", "tadi", "kemarin", "besok", "hari", "sampai",
}
STOPWORDS -= NEGASI

EMOJI_RE = re.compile(r"[\U0001F000-\U0001FAFF\U00002600-\U000027BF\U0000FE0F\U0000200D\U00002190-\U000021FF]+")
STEMMER = StemmerFactory().create_stemmer()
_STEM_CACHE: dict[str, str] = {}

bundle = joblib.load(MODEL_PATH)
PIPELINE = bundle["pipeline"]
MODEL_NAME = bundle.get("model_name", "Linear SVM")

app = FastAPI(title="HiOwy Bullying NLP Classifier")


class ClassifyRequest(BaseModel):
    report_id: int | None = None
    text: str
    category: str | None = None


def preprocess_tfidf(text: str) -> str:
    t = EMOJI_RE.sub(" ", str(text).lower())
    t = re.sub(r"http\S+|www\.\S+|@\w+", " ", t)
    t = t.replace("janc*k", "jancuk")
    t = re.sub(r"\b(\w+?)2an\b", r"\1\1an", t)
    t = re.sub(r"\b([a-z]+)2\b", r"\1 \1", t)
    t = re.sub(r"[^a-z\s]", " ", t)
    t = re.sub(r"([a-z])\1{2,}", r"\1", t)
    tokens = re.sub(r"\s+", " ", t).strip().split()
    norm = []
    for tok in tokens:
        norm.extend(SLANG_DICT.get(tok, tok).split())
    filtered = [tok for tok in norm if tok not in STOPWORDS and len(tok) > 1]
    stemmed = []
    for tok in filtered:
        if tok not in _STEM_CACHE:
            _STEM_CACHE[tok] = STEMMER.stem(tok)
        stemmed.append(_STEM_CACHE[tok])
    return " ".join(stemmed)


@app.post("/v1/classify")
def classify(req: ClassifyRequest):
    clean = preprocess_tfidf(req.text)
    proba = PIPELINE.predict_proba([clean])[0]
    p_ringan, p_berat = float(proba[0]), float(proba[1])
    label = int(p_berat >= 0.5)
    return {
        "label": label,
        "confidence": round(max(p_ringan, p_berat), 4),
        "prob_ringan": round(p_ringan, 4),
        "prob_berat": round(p_berat, 4),
        "model_version": "linear-svm-bullying-v2",
        "model_name": MODEL_NAME,
    }
